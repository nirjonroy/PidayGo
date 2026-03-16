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

        $qualifiedLevelIds = $levelResolver->qualifyingLevels($user)->pluck('id');
        $plans = $this->visibleReservePlans($user->id, $qualifiedLevelIds, $activeReserve, $reserveEnabled, $walletBalance);
        $availablePlanCount = $plans->where('can_reserve', true)->count();
        $unlockedPlanCount = $plans->where('is_unlocked', true)->count();

        return view('reserve.index', [
            'walletBalance' => $walletBalance,
            'reserveAccountBalance' => $reserveAccountBalance,
            'level' => $level,
            'reserveEnabled' => $reserveEnabled,
            'plans' => $plans,
            'availablePlanCount' => $availablePlanCount,
            'unlockedPlanCount' => $unlockedPlanCount,
            'activeReserve' => $activeReserve,
            'recentReserveLedgers' => $recentReserveLedgers,
            'recentReserveSales' => $recentReserveSales,
        ]);
    }

    public function confirm(Request $request, UserLevelResolver $levelResolver, FeatureFlagService $featureFlagService, UserReserveService $userReserveService, WalletService $walletService): RedirectResponse
    {
        if (!$featureFlagService->isEnabled('reserve_enabled')) {
            return back()->withErrors(['reserve_plan_id' => 'Reserve is currently disabled.']);
        }

        $user = $request->user();
        $walletBalance = (float) $walletService->getBalance($user);
        $level = $levelResolver->resolve($user);
        if (!$level) {
            return back()->withErrors(['reserve_plan_id' => 'No eligible level found.']);
        }

        $data = $request->validate([
            'reserve_plan_id' => ['required', 'exists:reserve_plans,id'],
        ]);

        $activeReserve = UserReserve::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->first();
        if ($activeReserve) {
            return redirect()->route('reserve.sell.form')->withErrors(['reserve_plan_id' => 'You already have an active reserve. Please complete the PI sell first.']);
        }

        $qualifiedLevelIds = $levelResolver->qualifyingLevels($user)->pluck('id');
        $visiblePlans = $this->visibleReservePlans($user->id, $qualifiedLevelIds, null, true, $walletBalance);
        $plan = $visiblePlans->firstWhere('id', (int) $data['reserve_plan_id']);
        if (!$plan) {
            return back()->withErrors(['reserve_plan_id' => 'Invalid reserve plan.']);
        }

        if (!$plan->getAttribute('can_reserve')) {
            return back()->withErrors(['reserve_plan_id' => $plan->getAttribute('availability_note') ?: 'This reserve option is not available right now.']);
        }

        DB::transaction(function () use ($user, $plan, $userReserveService, $walletService) {
            $reserveAmount = (float) $plan->getAttribute('computed_reserve_amount');

            $walletService->debit($user, 'reserve_lock', $reserveAmount, [
                'reserve_plan_id' => $plan->id,
                'reserve_percentage' => $plan->getAttribute('reserve_percentage'),
                'level_id' => $plan->level_id,
                'range_min' => $plan->getAttribute('range_min'),
                'range_max' => $plan->getAttribute('range_max'),
            ], $plan);

            $userReserveService->creditUserReserve(
                $user,
                $reserveAmount,
                'reserve_add',
                'reserve_plan',
                $plan->id
            );

            UserReserve::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level_id' => $plan->level_id,
                    'reserve_plan_id' => $plan->id,
                    'amount' => $reserveAmount,
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'completed_at' => null,
                    'meta' => [
                        'wallet_balance_min' => $plan->getAttribute('range_min'),
                        'wallet_balance_max' => $plan->getAttribute('range_max'),
                        'reserve_percentage' => $plan->getAttribute('reserve_percentage'),
                        'range_min' => $plan->getAttribute('range_min'),
                        'range_max' => $plan->getAttribute('range_max'),
                        'range_label' => $plan->getAttribute('range_label'),
                        'profit_min_percent' => $plan->profit_min_percent,
                        'profit_max_percent' => $plan->profit_max_percent,
                        'daily_limit' => $plan->max_sells_per_day,
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

        return redirect()->route('reserve.sell.form')->with('status', 'Reserve confirmed. The reserve amount was deducted from wallet and moved to reserve balance. Continue to Buy PI and sell to receive it back with profit.');
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

        DB::transaction(function () use ($request, $reserve, $saleAmount, $percent, $profit, $walletService, $chainService, $nftItemId, $userReserveService) {
            $sale = NftSale::create([
                'user_id' => $request->user()->id,
                'user_reserve_id' => $reserve->id,
                'nft_item_id' => $nftItemId,
                'sale_amount' => $saleAmount,
                'profit_percent' => $percent,
                'profit_amount' => $profit,
                'status' => 'paid',
            ]);

            $userReserveService->debitUserReserve(
                $request->user(),
                $saleAmount,
                'reserve_release',
                'reserve',
                $reserve->id
            );

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

        return redirect()->route('reserve.index')->with('status', 'PI sold successfully. Reserve amount and profit were returned to your wallet.');
    }

    private function visibleReservePlans(int $userId, Collection $qualifiedLevelIds, ?UserReserve $activeReserve, bool $reserveEnabled, float $walletBalance): Collection
    {
        $dailyStarts = UserReserveLedger::query()
            ->select('ref_id', DB::raw('COUNT(*) as total'))
            ->where('user_id', $userId)
            ->where('reason', 'reserve_started')
            ->where('ref_type', 'reserve_plan')
            ->whereDate('created_at', now()->toDateString())
            ->groupBy('ref_id')
            ->pluck('total', 'ref_id');

        return ReservePlan::query()
            ->with('level')
            ->where('is_active', true)
            ->orderBy('level_id')
            ->orderBy('wallet_balance_min')
            ->orderBy('reserve_amount')
            ->get()
            ->map(function (ReservePlan $plan) use ($qualifiedLevelIds, $activeReserve, $reserveEnabled, $dailyStarts, $walletBalance) {
                [$rangeMin, $rangeMax, $rangeLabel] = $this->resolveWalletBalanceRange($plan);
                $reservePercentage = (float) $plan->reserve_amount;
                $computedReserveAmount = round(($walletBalance * $reservePercentage) / 100, 8);
                $isUnlocked = $qualifiedLevelIds->contains($plan->level_id);
                $usedToday = (int) ($dailyStarts[$plan->id] ?? 0);
                $dailyLimit = $plan->max_sells_per_day;
                $dailyRemaining = $dailyLimit === null ? null : max(0, (int) $dailyLimit - $usedToday);
                $isActivePlan = !empty($activeReserve) && (int) $activeReserve->reserve_plan_id === (int) $plan->id;
                $hasConfiguredRange = $rangeMax > 0 || $rangeMin > 0;
                $isWithinWalletRange = $hasConfiguredRange
                    && $walletBalance >= $rangeMin
                    && ($rangeMax <= 0 || $walletBalance <= $rangeMax);
                $canReserve = $reserveEnabled
                    && $isUnlocked
                    && empty($activeReserve)
                    && $hasConfiguredRange
                    && $isWithinWalletRange
                    && $computedReserveAmount > 0
                    && ($dailyRemaining === null || $dailyRemaining > 0);

                if (!$reserveEnabled) {
                    $availabilityNote = 'Reserve is currently disabled.';
                    $actionLabel = 'Unavailable';
                } elseif (!$hasConfiguredRange) {
                    $availabilityNote = 'This level does not have a reserve amount range configured.';
                    $actionLabel = 'Unavailable';
                } elseif (!$isUnlocked) {
                    $availabilityNote = 'Requires ' . ($plan->level?->code ?? 'a higher level') . '.';
                    $actionLabel = 'Locked';
                } elseif (!empty($activeReserve)) {
                    $availabilityNote = $isActivePlan
                        ? 'This reserve is active. Continue to Buy PI.'
                        : 'Complete your current reserve first.';
                    $actionLabel = $isActivePlan ? 'Go to Buy PI' : 'Unavailable';
                } elseif ($dailyRemaining !== null && $dailyRemaining <= 0) {
                    $availabilityNote = 'Daily limit reached for this plan.';
                    $actionLabel = 'Limit Reached';
                } elseif (!$isWithinWalletRange) {
                    $availabilityNote = 'Requires wallet balance in the range ' . $rangeLabel . '.';
                    $actionLabel = 'Not Applicable';
                } else {
                    $availabilityNote = 'Available now.';
                    $actionLabel = 'Reserve Now';
                }

                $plan->setAttribute('range_min', $rangeMin);
                $plan->setAttribute('range_max', $rangeMax);
                $plan->setAttribute('range_label', $rangeLabel);
                $plan->setAttribute('reserve_percentage', $reservePercentage);
                $plan->setAttribute('computed_reserve_amount', $computedReserveAmount);
                $plan->setAttribute('computed_reserve_label', $this->formatDisplayAmount($computedReserveAmount) . ' USDT');
                $plan->setAttribute('is_unlocked', $isUnlocked);
                $plan->setAttribute('used_today', $usedToday);
                $plan->setAttribute('daily_remaining', $dailyRemaining);
                $plan->setAttribute('can_reserve', $canReserve);
                $plan->setAttribute('is_active_plan', $isActivePlan);
                $plan->setAttribute('availability_note', $availabilityNote);
                $plan->setAttribute('action_label', $actionLabel);

                return $plan;
            });
    }

    private function resolveWalletBalanceRange(ReservePlan $plan): array
    {
        $min = (float) ($plan->wallet_balance_min ?? 0);
        $max = (float) ($plan->wallet_balance_max ?? 0);

        if ($min <= 0 && $max <= 0) {
            $level = $plan->level;
            if (!$level) {
                return [0.0, 0.0, 'Not configured'];
            }

            $depositMin = (float) ($level->min_deposit ?? 0);
            $depositMax = (float) ($level->max_deposit ?? 0);
            $reservationMin = (float) ($level->min_reservation ?? 0);
            $reservationMax = (float) ($level->max_reservation ?? 0);

            $min = $depositMin > 0 || $depositMax > 0 ? $depositMin : $reservationMin;
            $max = $depositMin > 0 || $depositMax > 0 ? $depositMax : $reservationMax;
        }

        if ($min <= 0 && $max <= 0) {
            return [0.0, 0.0, 'Not configured'];
        }

        $max = $max > 0 ? $max : $min;

        return [
            $min,
            $max,
            $this->formatDisplayAmount($min) . ' - ' . $this->formatDisplayAmount($max) . ' USDT',
        ];
    }

    private function formatDisplayAmount(float $amount): string
    {
        return rtrim(rtrim(number_format($amount, 8, '.', ''), '0'), '.');
    }
}
