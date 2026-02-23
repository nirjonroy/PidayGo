<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserReserve;
use App\Models\UserReserveLedger;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UserReserveService
{
    public function getBalance(User $user): float
    {
        $reserve = $this->getOrCreateReserve($user);
        return (float) $reserve->reserved_balance;
    }

    public function creditUserReserve(User $user, float $amount, string $reason, ?string $refType = null, ?int $refId = null): UserReserveLedger
    {
        return DB::transaction(function () use ($user, $amount, $reason, $refType, $refId) {
            $reserve = $this->getOrCreateReserve($user, true);

            $ledger = UserReserveLedger::create([
                'user_id' => $user->id,
                'change' => $amount,
                'reason' => $reason,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'created_at' => now(),
            ]);

            $reserve->reserved_balance = round((float) $reserve->reserved_balance + $amount, 8);
            $reserve->save();

            return $ledger;
        });
    }

    public function debitUserReserve(User $user, float $amount, string $reason, ?string $refType = null, ?int $refId = null): UserReserveLedger
    {
        return DB::transaction(function () use ($user, $amount, $reason, $refType, $refId) {
            $reserve = $this->getOrCreateReserve($user, true);

            $balance = (float) $reserve->reserved_balance;
            if ($balance < $amount) {
                throw new RuntimeException('Insufficient reserve balance.');
            }

            $ledger = UserReserveLedger::create([
                'user_id' => $user->id,
                'change' => -$amount,
                'reason' => $reason,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'created_at' => now(),
            ]);

            $reserve->reserved_balance = round($balance - $amount, 8);
            $reserve->save();

            return $ledger;
        });
    }

    private function getOrCreateReserve(User $user, bool $lockForUpdate = false): UserReserve
    {
        if ($lockForUpdate) {
            $reserve = UserReserve::where('user_id', $user->id)->lockForUpdate()->first();
            if ($reserve) {
                return $reserve;
            }
        } else {
            $reserve = UserReserve::where('user_id', $user->id)->first();
            if ($reserve) {
                return $reserve;
            }
        }

        return UserReserve::create([
            'user_id' => $user->id,
            'reserved_balance' => 0,
        ]);
    }
}
