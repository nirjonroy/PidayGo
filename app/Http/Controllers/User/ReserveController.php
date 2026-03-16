<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NftItem;
use App\Models\NftSale;
use App\Models\ReservePlan;
use App\Models\ReservePlanRange;
use App\Models\UserReserve;
use App\Models\UserReserveLedger;
use App\Services\FeatureFlagService;
use App\Services\NotificationService;
use App\Services\ReferralChainService;
use App\Services\UserLevelResolver;
use App\Services\UserReserveService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        $reserveOptions = $this->visibleReserveOptions($user->id, $qualifiedLevelIds, $activeReserve, $reserveEnabled, $walletBalance);
        $availableOptionCount = $reserveOptions->where('can_reserve', true)->count();
        $unlockedOptionCount = $reserveOptions->where('is_unlocked', true)->count();

        return view('reserve.index', [
            'walletBalance' => $walletBalance,
            'reserveAccountBalance' => $reserveAccountBalance,
            'level' => $level,
            'reserveEnabled' => $reserveEnabled,
            'reserveOptions' => $reserveOptions,
            'availableOptionCount' => $availableOptionCount,
            'unlockedOptionCount' => $unlockedOptionCount,
            'activeReserve' => $activeReserve,
            'recentReserveLedgers' => $recentReserveLedgers,
            'recentReserveSales' => $recentReserveSales,
        ]);
    }

    public function confirm(Request $request, UserLevelResolver $levelResolver, FeatureFlagService $featureFlagService, UserReserveService $userReserveService, WalletService $walletService, NotificationService $notifications): RedirectResponse
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
            'reserve_plan_range_id' => ['required', 'exists:reserve_plan_ranges,id'],
        ]);

        $activeReserve = UserReserve::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->first();
        if ($activeReserve) {
            return redirect()->route('reserve.sell.form')->withErrors(['reserve_plan_id' => 'You already have an active reserve. Please complete the PI sell first.']);
        }

        $qualifiedLevelIds = $levelResolver->qualifyingLevels($user)->pluck('id');
        $visibleOptions = $this->visibleReserveOptions($user->id, $qualifiedLevelIds, null, true, $walletBalance);
        $option = $visibleOptions->first(function (ReservePlanRange $range) use ($data) {
            return (int) $range->reserve_plan_id === (int) $data['reserve_plan_id']
                && (int) $range->id === (int) $data['reserve_plan_range_id'];
        });

        if (!$option) {
            return back()->withErrors(['reserve_plan_id' => 'Invalid reserve plan option.']);
        }

        if (!$option->getAttribute('can_reserve')) {
            return back()->withErrors(['reserve_plan_id' => $option->getAttribute('availability_note') ?: 'This reserve option is not available right now.']);
        }

        $reserveAmount = (float) $option->getAttribute('computed_reserve_amount');
        $plan = $option->plan;

        DB::transaction(function () use ($user, $option, $reserveAmount, $plan, $userReserveService, $walletService) {

            $walletService->debit($user, 'reserve_lock', $reserveAmount, [
                'reserve_plan_id' => $plan->id,
                'reserve_plan_range_id' => $option->id,
                'reserve_percentage' => $option->getAttribute('reserve_percentage'),
                'level_id' => $plan->level_id,
                'range_min' => $option->getAttribute('range_min'),
                'range_max' => $option->getAttribute('range_max'),
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
                        'reserve_plan_range_id' => $option->id,
                        'wallet_balance_min' => $option->getAttribute('range_min'),
                        'wallet_balance_max' => $option->getAttribute('range_max'),
                        'reserve_percentage' => $option->getAttribute('reserve_percentage'),
                        'range_min' => $option->getAttribute('range_min'),
                        'range_max' => $option->getAttribute('range_max'),
                        'range_label' => $option->getAttribute('range_label'),
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

        $notifications->notifyUser(
            $user->id,
            'reserve_started',
            'PI Reserve Confirmed',
            'Your PI reserve of ' . number_format($reserveAmount, 8) . ' USDT for ' . ($plan->level?->code ?? 'your level') . ' has been confirmed. Continue to Buy PI and complete the sell to receive your reserve back with profit.',
            'success',
            [
                'popup_icon' => 'pi',
                'reserve_plan_id' => $plan->id,
                'reserve_plan_range_id' => $option->id,
                'reserve_amount' => $reserveAmount,
                'range_label' => $option->getAttribute('range_label'),
            ],
            true
        );

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

    public function sellSubmit(Request $request, WalletService $walletService, ReferralChainService $chainService, FeatureFlagService $featureFlagService, UserReserveService $userReserveService, NotificationService $notifications): RedirectResponse
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

        $notifications->notifyUser(
            $request->user()->id,
            'reserve_completed',
            'PI Sell Completed',
            'Your PI sell completed successfully. ' . number_format($saleAmount, 8) . ' USDT reserve and ' . number_format($profit, 8) . ' USDT profit were credited to your wallet.',
            'success',
            [
                'popup_icon' => 'pi',
                'reserve_id' => $reserve->id,
                'reserve_amount' => $saleAmount,
                'profit_amount' => $profit,
                'profit_percent' => $percent,
                'nft_item_id' => $nftItemId,
            ],
            true
        );

        return redirect()->route('reserve.index')->with('status', 'PI sold successfully. Reserve amount and profit were returned to your wallet.');
    }

    private function visibleReserveOptions(int $userId, Collection $qualifiedLevelIds, ?UserReserve $activeReserve, bool $reserveEnabled, float $walletBalance): Collection
    {
        $dailyStarts = UserReserveLedger::query()
            ->select('ref_id', DB::raw('COUNT(*) as total'))
            ->where('user_id', $userId)
            ->where('reason', 'reserve_started')
            ->where('ref_type', 'reserve_plan')
            ->whereDate('created_at', now()->toDateString())
            ->groupBy('ref_id')
            ->pluck('total', 'ref_id');

        $activeRangeId = (int) data_get($activeReserve?->meta, 'reserve_plan_range_id', 0);

        return ReservePlan::query()
            ->with(['level', 'ranges'])
            ->where('is_active', true)
            ->orderBy('level_id')
            ->get()
            ->flatMap(function (ReservePlan $plan) {
                if ($plan->ranges->isNotEmpty()) {
                    return $plan->ranges;
                }

                $fallbackRange = new ReservePlanRange([
                    'reserve_plan_id' => $plan->id,
                    'wallet_balance_min' => $plan->wallet_balance_min,
                    'wallet_balance_max' => $plan->wallet_balance_max,
                    'reserve_percentage' => $plan->reserve_amount,
                ]);
                $fallbackRange->id = 0;
                $fallbackRange->setRelation('plan', $plan);

                return collect([$fallbackRange]);
            })
            ->map(function (ReservePlanRange $range) use ($qualifiedLevelIds, $activeReserve, $activeRangeId, $reserveEnabled, $dailyStarts, $walletBalance) {
                $plan = $range->plan;
                [$rangeMin, $rangeMax, $rangeLabel] = $this->resolveWalletBalanceRange($range);
                $reservePercentage = (float) $range->reserve_percentage;
                $computedReserveAmount = round(($walletBalance * $reservePercentage) / 100, 8);
                $isUnlocked = $qualifiedLevelIds->contains($plan->level_id);
                $usedToday = (int) ($dailyStarts[$plan->id] ?? 0);
                $dailyLimit = $plan->max_sells_per_day;
                $dailyRemaining = $dailyLimit === null ? null : max(0, (int) $dailyLimit - $usedToday);
                $isActivePlan = !empty($activeReserve) && (int) $activeReserve->reserve_plan_id === (int) $plan->id;
                $isActiveOption = $isActivePlan && ($activeRangeId === 0 || $activeRangeId === (int) $range->id);
                $hasConfiguredRange = $rangeMax > 0 || $rangeMin > 0;
                $canReserve = $reserveEnabled
                    && $isUnlocked
                    && empty($activeReserve)
                    && $hasConfiguredRange
                    && $reservePercentage > 0
                    && $computedReserveAmount > 0
                    && ($dailyRemaining === null || $dailyRemaining > 0);

                if (!$reserveEnabled) {
                    $availabilityNote = 'Reserve is currently disabled.';
                    $actionLabel = 'Unavailable';
                } elseif (!$hasConfiguredRange) {
                    $availabilityNote = 'This reserve option does not have a wallet balance range configured.';
                    $actionLabel = 'Unavailable';
                } elseif (!$isUnlocked) {
                    $availabilityNote = 'Requires ' . ($plan->level?->code ?? 'a higher level') . '.';
                    $actionLabel = 'Locked';
                } elseif (!empty($activeReserve)) {
                    $availabilityNote = $isActiveOption
                        ? 'This reserve is active. Continue to Buy PI.'
                        : 'Complete your current reserve first.';
                    $actionLabel = $isActiveOption ? 'Go to Buy PI' : 'Unavailable';
                } elseif ($dailyRemaining !== null && $dailyRemaining <= 0) {
                    $availabilityNote = 'Daily limit reached for this plan.';
                    $actionLabel = 'Limit Reached';
                } else {
                    $availabilityNote = 'Available now for your current level.';
                    $actionLabel = 'Reserve Now';
                }

                $range->setAttribute('level_id', $plan->level_id);
                $range->setAttribute('level_label', $plan->level?->code ?? 'Reserve');
                $range->setAttribute('range_min', $rangeMin);
                $range->setAttribute('range_max', $rangeMax);
                $range->setAttribute('range_label', $rangeLabel);
                $range->setAttribute('reserve_percentage', $reservePercentage);
                $range->setAttribute('computed_reserve_amount', $computedReserveAmount);
                $range->setAttribute('computed_reserve_label', $this->formatDisplayAmount($computedReserveAmount) . ' USDT');
                $range->setAttribute('is_unlocked', $isUnlocked);
                $range->setAttribute('used_today', $usedToday);
                $range->setAttribute('daily_remaining', $dailyRemaining);
                $range->setAttribute('can_reserve', $canReserve);
                $range->setAttribute('is_active_option', $isActiveOption);
                $range->setAttribute('availability_note', $availabilityNote);
                $range->setAttribute('action_label', $actionLabel);

                return $range;
            })
            ->sortBy(function (ReservePlanRange $range) {
                return sprintf(
                    '%010d-%020.8F-%020.8F',
                    (int) $range->getAttribute('level_id'),
                    (float) ($range->wallet_balance_min ?? 0),
                    (float) ($range->wallet_balance_max ?? 0)
                );
            })
            ->values();
    }

    private function resolveWalletBalanceRange(ReservePlanRange $range): array
    {
        $min = (float) ($range->wallet_balance_min ?? 0);
        $max = (float) ($range->wallet_balance_max ?? 0);

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
