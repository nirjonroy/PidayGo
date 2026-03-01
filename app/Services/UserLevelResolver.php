<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;

class UserLevelResolver
{
    public function resolve(User $user): ?Level
    {
        $depositTotal = (float) $user->walletLedgers()->where('type', 'deposit')->sum('amount');

        $levels = Level::query()
            ->where('is_active', true)
            ->orderBy('min_deposit')
            ->get();

        foreach ($levels as $level) {
            $within = $depositTotal >= (float) $level->min_deposit && $depositTotal <= (float) $level->max_deposit;
            if (!$within) {
                continue;
            }

            $counts = [
                'A' => $user->referrals()->where('chain_slot', 'A')->count(),
                'B' => $user->referrals()->where('chain_slot', 'B')->count(),
                'C' => $user->referrals()->where('chain_slot', 'C')->count(),
            ];

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

        return $levels->first();
    }
}
