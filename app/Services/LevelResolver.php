<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;

class LevelResolver
{
    public function __construct(private UserReserveService $userReserveService)
    {
    }

    public function getUserLevel(User $user): ?Level
    {
        $balance = $this->userReserveService->getBalance($user);

        $levels = Level::query()
            ->where('is_active', true)
            ->orderBy('min_reservation')
            ->get();

        if ($levels->isEmpty()) {
            return null;
        }

        foreach ($levels as $level) {
            if ($balance >= (float) $level->min_reservation && $balance <= (float) $level->max_reservation) {
                return $level;
            }
        }

        return $levels->first();
    }
}
