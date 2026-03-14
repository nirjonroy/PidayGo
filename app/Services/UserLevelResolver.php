<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;

class UserLevelResolver
{
    public function __construct(private ReferralChainService $referralChainService)
    {
    }

    public function resolve(User $user): ?Level
    {
        $depositTotal = (float) $user->walletLedgers()->where('type', 'deposit')->sum('amount');
        $reservedTotal = (float) $user->reserves()
            ->where('status', 'confirmed')
            ->sum('amount');
        $counts = $this->referralChainService->getReferralDepthCounts($user);

        $levels = Level::query()
            ->where('is_active', true)
            ->orderByDesc('min_deposit')
            ->orderByDesc('min_reservation')
            ->orderByDesc('id')
            ->get();

        foreach ($levels as $level) {
            // Levels act as unlock thresholds. Once a user exceeds a lower tier's max range,
            // they should still keep the highest tier whose minimum requirements they meet.
            if ($depositTotal < (float) $level->min_deposit) {
                continue;
            }

            if ($reservedTotal < (float) $level->min_reservation) {
                continue;
            }

            if ($counts['A'] < (int) $level->req_chain_a) {
                continue;
            }
            if ($counts['B'] < (int) $level->req_chain_b) {
                continue;
            }
            if ($counts['C'] < (int) $level->req_chain_c) {
                continue;
            }

            return $level;
        }

        return null;
    }
}
