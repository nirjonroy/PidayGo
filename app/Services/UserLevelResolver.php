<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Collection;

class UserLevelResolver
{
    public function __construct(private ReferralChainService $referralChainService)
    {
    }

    public function resolve(User $user): ?Level
    {
        return $this->qualifyingLevels($user)->first();
    }

    public function qualifyingLevels(User $user): Collection
    {
        $depositTotal = (float) $user->walletLedgers()->where('type', 'deposit')->sum('amount');
        $counts = $this->referralChainService->getReferralDepthCounts($user);

        return Level::query()
            ->where('is_active', true)
            ->orderByDesc('min_deposit')
            ->orderByDesc('max_deposit')
            ->orderByDesc('id')
            ->get()
            ->filter(function (Level $level) use ($depositTotal, $counts) {
                if ($depositTotal < (float) $level->min_deposit) {
                    return false;
                }

                $maxDeposit = (float) ($level->max_deposit ?? 0);
                if ($maxDeposit > 0 && $depositTotal > $maxDeposit) {
                    return false;
                }

                return $counts['A'] >= (int) $level->req_chain_a
                    && $counts['B'] >= (int) $level->req_chain_b
                    && $counts['C'] >= (int) $level->req_chain_c;
            })
            ->values();
    }
}
