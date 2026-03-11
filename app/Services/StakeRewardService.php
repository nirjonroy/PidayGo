<?php

namespace App\Services;

use App\Models\Stake;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StakeRewardService
{
    public function creditDueRewardsForAll(WalletService $walletService): void
    {
        Stake::where('status', 'active')
            ->orderBy('id')
            ->chunkById(100, function ($stakes) use ($walletService) {
                foreach ($stakes as $stake) {
                    $this->creditDueRewardsForStake($stake, $walletService);
                }
            });
    }

    public function creditDueRewardsForUser(User $user, WalletService $walletService): void
    {
        Stake::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->chunkById(100, function ($stakes) use ($walletService) {
                foreach ($stakes as $stake) {
                    $this->creditDueRewardsForStake($stake, $walletService);
                }
            });
    }

    public function creditDueRewardsForStake(Stake $stake, WalletService $walletService): void
    {
        $now = now();

        DB::transaction(function () use ($stake, $walletService, $now) {
            $lockedStake = Stake::whereKey($stake->id)->lockForUpdate()->first();

            if (!$lockedStake || $lockedStake->status !== 'active') {
                return;
            }

            $lockedStake->loadMissing(['stakePlan', 'user']);
            $plan = $lockedStake->stakePlan;
            $user = $lockedStake->user;

            if (!$plan || !$user) {
                return;
            }

            $base = ($lockedStake->last_reward_at ?? $lockedStake->started_at)?->copy();
            if (!$base) {
                return;
            }

            $eligibleThrough = $now->copy();
            if ($lockedStake->ends_at && $lockedStake->ends_at->lt($eligibleThrough)) {
                $eligibleThrough = $lockedStake->ends_at->copy();
            }

            $fullDays = $base->diffInDays($eligibleThrough);

            $principal = (float) $lockedStake->principal_amount;
            $dailyRate = (float) $plan->daily_rate;
            $perDayReward = round(($principal * $dailyRate) / 100, 8);

            $maxPayout = $principal * (float) ($plan->max_payout_multiplier ?? 0);
            $remaining = $maxPayout - (float) $lockedStake->total_reward_paid;

            if ($remaining <= 0) {
                $lockedStake->status = 'completed';
                $lockedStake->save();
                return;
            }

            $daysCredited = 0;

            for ($i = 0; $i < $fullDays; $i++) {
                if ($remaining <= 0) {
                    break;
                }

                $creditDate = $base->copy()->addDays($i + 1);
                $reward = min($perDayReward, $remaining);

                if ($reward <= 0) {
                    break;
                }

                $walletService->credit(
                    $user,
                    'reward_credit',
                    $reward,
                    ['day' => $creditDate->toDateString(), 'rate' => (string) $plan->daily_rate],
                    $lockedStake
                );

                $lockedStake->total_reward_paid = round(
                    (float) $lockedStake->total_reward_paid + $reward,
                    8
                );
                $remaining = $maxPayout - (float) $lockedStake->total_reward_paid;
                $daysCredited++;
            }

            if ($daysCredited > 0) {
                $lockedStake->last_reward_at = $base->copy()->addDays($daysCredited);
            }

            if (($lockedStake->ends_at && $now->gte($lockedStake->ends_at)) || $remaining <= 0) {
                $lockedStake->status = 'completed';
            }

            $lockedStake->save();
        });
    }
}
