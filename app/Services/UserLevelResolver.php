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
        $reservedTotal = (float) ($user->reserve?->reserved_balance ?? 0);
        $counts = $this->referralChainService->getReferralDepthCounts($user);

        return Level::query()
            ->where('is_active', true)
            ->orderByDesc('min_deposit')
            ->orderByDesc('min_reservation')
            ->orderByDesc('id')
            ->get()
            ->filter(function (Level $level) use ($depositTotal, $reservedTotal, $counts) {
                if ($depositTotal < (float) $level->min_deposit) {
                    return false;
                }

                if ($reservedTotal < (float) $level->min_reservation) {
                    return false;
                }

                return $counts['A'] >= (int) $level->req_chain_a
                    && $counts['B'] >= (int) $level->req_chain_b
                    && $counts['C'] >= (int) $level->req_chain_c;
            })
            ->values();
    }
}
