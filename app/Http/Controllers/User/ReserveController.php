<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
use Illuminate\View\View;

class ReserveController extends Controller
{
    public function index(Request $request, UserReserveService $userReserveService, UserLevelResolver $levelResolver, WalletService $walletService, FeatureFlagService $featureFlagService): View
    {
        $user = $request->user();
        $walletBalance = (float) $walletService->getBalance($user);
        $reservedBalance = (float) UserReserve::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');
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

        $plans = collect();
        if ($level) {
            $plans = ReservePlan::where('level_id', $level->id)->where('is_active', true)->orderBy('reserve_amount')->get();
        }

        $selectedPlanId = null;
        if ($plans->count() === 1) {
            $selectedPlanId = $plans->first()->id;
        } elseif ($plans->count() > 1) {
            $selectedPlan = $plans->where('reserve_amount', '<=', $walletBalance)->last();
            $selectedPlanId = $selectedPlan?->id ?? $plans->first()->id;
        }

        return view('reserve.index', [
            'walletBalance' => $walletBalance,
            'reservedBalance' => $reservedBalance,
            'level' => $level,
            'reserveEnabled' => $reserveEnabled,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'activeReserve' => $activeReserve,
            'recentReserveLedgers' => $recentReserveLedgers,
            'recentReserveSales' => $recentReserveSales,
        ]);
    }

    public function confirm(Request $request, UserReserveService $userReserveService, UserLevelResolver $levelResolver, WalletService $walletService, FeatureFlagService $featureFlagService): RedirectResponse
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

        $plan = ReservePlan::where('id', $data['reserve_plan_id'])
            ->where('level_id', $level->id)
            ->where('is_active', true)
            ->first();
        if (!$plan) {
            return back()->withErrors(['reserve_plan_id' => 'Invalid reserve plan.']);
        }

        $walletBalance = (float) $walletService->getBalance($user);
        if ($walletBalance < (float) $plan->reserve_amount) {
            return back()->withErrors(['reserve_plan_id' => 'Insufficient wallet balance.']);
        }

        $hasActive = UserReserve::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->exists();
        if ($hasActive) {
            return redirect()->route('reserve.sell.form')->withErrors(['reserve_plan_id' => 'You already have an active reserve.']);
        }

        DB::transaction(function () use ($user, $plan, $level, $walletService, $userReserveService) {
            $reserve = UserReserve::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level_id' => $level->id,
                    'reserve_plan_id' => $plan->id,
                    'amount' => $plan->reserve_amount,
                    'reserved_balance' => $plan->reserve_amount,
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'meta' => [
                        'profit_min_percent' => $plan->profit_min_percent,
                        'profit_max_percent' => $plan->profit_max_percent,
                    ],
                ]
            );

            $walletService->debit($user, 'reserve_lock', $plan->reserve_amount, [
                'level_id' => $level->id,
                'reserve_plan_id' => $plan->id,
            ], $reserve);

            $userReserveService->creditUserReserve(
                $user,
                (float) $plan->reserve_amount,
                'reserve_add',
                'reserve_plan',
                $plan->id
            );
        });

        return redirect()->route('reserve.sell.form')->with('status', 'Reserve confirmed successfully.');
    }

    public function sellForm(Request $request, FeatureFlagService $featureFlagService): View|RedirectResponse
    {
        if (!$featureFlagService->isEnabled('reserve_enabled')) {
            return redirect()->route('reserve.index')->withErrors(['reserve_plan_id' => 'Reserve is disabled.']);
        }

        $reserve = UserReserve::where('user_id', $request->user()->id)
            ->where('status', 'confirmed')
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

        $sellCount = NftSale::where('user_reserve_id', $reserve->id)->count();
        $sellLimit = $reserve->plan?->max_sells;
        $sellsRemaining = $sellLimit === null ? null : max(0, (int) $sellLimit - (int) $sellCount);
        $dailyLimit = $reserve->plan?->max_sells_per_day;
        $dailyCount = NftSale::where('user_reserve_id', $reserve->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $dailyRemaining = $dailyLimit === null ? null : max(0, (int) $dailyLimit - (int) $dailyCount);

        return view('reserve.sell', [
            'reserve' => $reserve,
            'plan' => $reserve->plan,
            'level' => $reserve->level,
            'items' => $items,
            'nftEnabled' => $featureFlagService->isEnabled('nft_enabled'),
            'sellLimit' => $sellLimit,
            'sellCount' => $sellCount,
            'sellsRemaining' => $sellsRemaining,
            'dailyLimit' => $dailyLimit,
            'dailyCount' => $dailyCount,
            'dailyRemaining' => $dailyRemaining,
        ]);
    }

    public function sellSubmit(Request $request, WalletService $walletService, ReferralChainService $chainService, FeatureFlagService $featureFlagService, UserReserveService $userReserveService): RedirectResponse
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

        $sellLimit = $reserve->plan?->max_sells;
        $unlockPolicy = $reserve->plan?->unlock_policy ?? 'never';
        if ($sellLimit !== null) {
            $sellCount = NftSale::where('user_reserve_id', $reserve->id)->count();
            if ($sellCount >= (int) $sellLimit) {
                return back()->withErrors(['sale_amount' => 'Sell limit reached for this reserve.']);
            }
        }
        $dailyLimit = $reserve->plan?->max_sells_per_day;
        if ($dailyLimit !== null) {
            $dailyCount = NftSale::where('user_reserve_id', $reserve->id)
                ->whereDate('created_at', now()->toDateString())
                ->count();
            if ($dailyCount >= (int) $dailyLimit) {
                return back()->withErrors(['sale_amount' => 'Daily sell limit reached for this reserve.']);
            }
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

        DB::transaction(function () use ($request, $reserve, $saleAmount, $percent, $profit, $walletService, $chainService, $nftItemId, $userReserveService, $sellLimit, $unlockPolicy) {
            $sale = NftSale::create([
                'user_id' => $request->user()->id,
                'user_reserve_id' => $reserve->id,
                'nft_item_id' => $nftItemId,
                'sale_amount' => $saleAmount,
                'profit_percent' => $percent,
                'profit_amount' => $profit,
                'status' => 'paid',
            ]);

            $walletService->credit($request->user(), 'nft_profit', $profit, [
                'sale_amount' => $saleAmount,
                'percent' => $percent,
                'reserve_id' => $reserve->id,
                'nft_item_id' => $nftItemId,
            ], $sale);

            $chainService->distributeCommissionFromSale($sale, $walletService);

            if ($unlockPolicy === 'after_sells' && $sellLimit !== null) {
                $newCount = NftSale::where('user_reserve_id', $reserve->id)->count();
                if ($newCount >= (int) $sellLimit) {
                    $releaseAmount = (float) ($reserve->amount ?? $reserve->reserved_balance);
                    $walletService->credit($request->user(), 'reserve_release', $releaseAmount, [
                        'reserve_id' => $reserve->id,
                    ], $reserve);

                    $userReserveService->debitUserReserve(
                        $request->user(),
                        $releaseAmount,
                        'reserve_release',
                        'reserve',
                        $reserve->id
                    );

                    $reserve->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('status', 'Profit credited to wallet.');
    }
}
