<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ReservePlan;
use App\Models\NftItem;
use App\Models\UserReserve;
use App\Models\UserReserveLedger;
use App\Models\NftSale;
use App\Services\FeatureFlagService;
use App\Services\UserLevelResolver;
use App\Services\UserReserveService;
use App\Services\WalletService;
use App\Services\ReferralChainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReserveController extends Controller
{
    public function index(Request $request, UserReserveService $userReserveService, UserLevelResolver $levelResolver, WalletService $walletService, FeatureFlagService $featureFlagService): View
    {
        $user = $request->user();
        $walletBalance = (float) $walletService->getBalance($user);
        $reserveAccountBalance = (float) $userReserveService->getBalance($user);
        $activeReserve = UserReserve::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->with(['plan', 'level'])
            ->orderByDesc('updated_at')
            ->first();
        $level = $levelResolver->resolve($user);
        $reserveEnabled = $featureFlagService->isEnabled('reserve_enabled');
        $recentReserveLedgers = UserReserveLedger::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
        $recentReserveSales = NftSale::query()
            ->where('user_id', $user->id)
            ->with(['nftItem'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $plans = $this->eligibleReservePlans($level);
        $reservedToday = UserReserve::query()
            ->where('user_id', $user->id)
            ->whereDate('confirmed_at', now()->toDateString())
            ->exists();

        return view('reserve.index', [
            'walletBalance' => $walletBalance,
            'reserveAccountBalance' => $reserveAccountBalance,
            'level' => $level,
            'reserveEnabled' => $reserveEnabled,
            'plans' => $plans,
            'reservedToday' => $reservedToday,
            'activeReserve' => $activeReserve,
            'recentReserveLedgers' => $recentReserveLedgers,
            'recentReserveSales' => $recentReserveSales,
        ]);
    }

    public function confirm(Request $request, UserLevelResolver $levelResolver, FeatureFlagService $featureFlagService): RedirectResponse
    {
        if (!$featureFlagService->isEnabled('reserve_enabled')) {
            return back()->withErrors(['reserve_plan_id' => 'Reserve is currently disabled.']);
        }

        $user = $request->user();
        $level = $levelResolver->resolve($user);
        if (!$level) {
            return back()->withErrors(['reserve_plan_id' => 'No eligible level found.']);
        }

        $data = $request->validate([
            'reserve_plan_id' => ['required', 'exists:reserve_plans,id'],
        ]);

        $eligiblePlans = $this->eligibleReservePlans($level);
        $plan = $eligiblePlans->firstWhere('id', (int) $data['reserve_plan_id']);
        if (!$plan) {
            return back()->withErrors(['reserve_plan_id' => 'Invalid reserve plan.']);
        }

        $activeReserve = UserReserve::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->first();
        if ($activeReserve) {
            return redirect()->route('reserve.sell.form')->withErrors(['reserve_plan_id' => 'You already have an active reserve. Please complete the PI sell first.']);
        }

        $reservedToday = UserReserve::query()
            ->where('user_id', $user->id)
            ->whereDate('confirmed_at', now()->toDateString())
            ->exists();
        if ($reservedToday) {
            return back()->withErrors(['reserve_plan_id' => 'You can reserve only once per day. Please try again tomorrow.']);
        }

        DB::transaction(function () use ($user, $plan) {
            UserReserve::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level_id' => $plan->level_id,
                    'reserve_plan_id' => $plan->id,
                    'amount' => $plan->reserve_amount,
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'completed_at' => null,
                    'meta' => [
                        'profit_min_percent' => $plan->profit_min_percent,
                        'profit_max_percent' => $plan->profit_max_percent,
                        'daily_limit' => 1,
                    ],
                ]
            );

            UserReserveLedger::create([
                'user_id' => $user->id,
                'change' => 0,
                'reason' => 'reserve_started',
                'ref_type' => 'reserve_plan',
                'ref_id' => $plan->id,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('reserve.sell.form')->with('status', 'Reserve confirmed. Continue to Buy PI and sell to receive your reserve amount plus profit.');
    }

    public function sellForm(Request $request, FeatureFlagService $featureFlagService): View|RedirectResponse
    {
        if (!$featureFlagService->isEnabled('reserve_enabled')) {
            return redirect()->route('reserve.index')->withErrors(['reserve_plan_id' => 'Reserve is disabled.']);
        }

        $reserve = UserReserve::where('user_id', $request->user()->id)
            ->where('status', 'confirmed')
            ->with(['plan', 'level'])
            ->first();

        if (!$reserve) {
            return redirect()->route('reserve.index')->withErrors(['reserve_plan_id' => 'Confirm a reserve to sell.']);
        }

        $items = NftItem::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->orderByDesc('is_featured')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        return view('reserve.sell', [
            'reserve' => $reserve,
            'plan' => $reserve->plan,
            'level' => $reserve->level,
            'items' => $items,
            'nftEnabled' => $featureFlagService->isEnabled('nft_enabled'),
        ]);
    }

    public function sellSubmit(Request $request, WalletService $walletService, ReferralChainService $chainService, FeatureFlagService $featureFlagService): RedirectResponse
    {
        if (!$featureFlagService->isEnabled('nft_enabled')) {
            return back()->withErrors(['sale_amount' => 'Sell is disabled.']);
        }

        $data = $request->validate([
            'nft_item_id' => ['required', 'exists:nft_items,id'],
        ]);

        $reserve = UserReserve::where('user_id', $request->user()->id)
            ->where('status', 'confirmed')
            ->first();

        if (!$reserve || !$reserve->plan) {
            return back()->withErrors(['sale_amount' => 'No active reserve found.']);
        }

        $saleAmount = (float) ($reserve->amount ?? $reserve->reserved_balance);
        $nftItemId = (int) $data['nft_item_id'];

        $nftItem = NftItem::query()
            ->where('id', $nftItemId)
            ->where('is_active', true)
            ->where('status', 'published')
            ->first();
        if (!$nftItem) {
            return back()->withErrors(['sale_amount' => 'Selected NFT is not available.']);
        }
        $min = (float) $reserve->plan->profit_min_percent;
        $max = (float) $reserve->plan->profit_max_percent;
        $percent = $min;
        if ($max > $min) {
            $percent = random_int((int) round($min * 1000), (int) round($max * 1000)) / 1000;
        }
        $profit = ($saleAmount * $percent) / 100;

        DB::transaction(function () use ($request, $reserve, $saleAmount, $percent, $profit, $walletService, $chainService, $nftItemId) {
            $sale = NftSale::create([
                'user_id' => $request->user()->id,
                'user_reserve_id' => $reserve->id,
                'nft_item_id' => $nftItemId,
                'sale_amount' => $saleAmount,
                'profit_percent' => $percent,
                'profit_amount' => $profit,
                'status' => 'paid',
            ]);

            $walletService->credit($request->user(), 'reserve_release', $saleAmount, [
                'reserve_id' => $reserve->id,
                'reserve_plan_id' => $reserve->reserve_plan_id,
            ], $reserve);

            $walletService->credit($request->user(), 'nft_profit', $profit, [
                'sale_amount' => $saleAmount,
                'percent' => $percent,
                'reserve_id' => $reserve->id,
                'nft_item_id' => $nftItemId,
            ], $sale);

            $chainService->distributeCommissionFromSale($sale, $walletService);

            UserReserveLedger::create([
                'user_id' => $request->user()->id,
                'change' => $saleAmount,
                'reason' => 'reserve_completed',
                'ref_type' => 'reserve_sale',
                'ref_id' => $sale->id,
                'created_at' => now(),
            ]);

            $reserve->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('reserve.index')->with('status', 'PI sold successfully. Reserve amount and profit were added to your wallet.');
    }

    private function eligibleReservePlans(?Level $level): Collection
    {
        if (!$level) {
            return collect();
        }

        $eligibleLevelIds = Level::query()
            ->where('is_active', true)
            ->orderBy('min_deposit')
            ->get()
            ->filter(fn (Level $candidate) => (float) $candidate->min_deposit <= (float) $level->min_deposit)
            ->pluck('id');

        return ReservePlan::query()
            ->with('level')
            ->where('is_active', true)
            ->whereIn('level_id', $eligibleLevelIds)
            ->orderBy('level_id')
            ->orderBy('reserve_amount')
            ->get();
    }
}
