<?php

namespace App\Services;

use App\Models\ReserveAccount;
use App\Models\ReserveLedger;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReserveService
{
    public function getBalance(string $currency = 'USDT'): float
    {
        $reserve = ReserveAccount::where('currency', $currency)->first();
        return (float) ($reserve?->balance ?? 0);
    }

    public function credit(float $amount, string $reason, ?string $refType = null, ?int $refId = null, ?int $adminId = null, string $currency = 'USDT'): ReserveLedger
    {
        return DB::transaction(function () use ($amount, $reason, $refType, $refId, $adminId, $currency) {
            $reserve = ReserveAccount::where('currency', $currency)->lockForUpdate()->first();
            if (!$reserve) {
                $reserve = ReserveAccount::create([
                    'currency' => $currency,
                    'balance' => 0,
                ]);
            }

            $ledger = ReserveLedger::create([
                'reserve_account_id' => $reserve->id,
                'amount' => $amount,
                'reason' => $reason,
                'created_by_admin_id' => $adminId,
                'meta' => array_filter([
                    'ref_type' => $refType,
                    'ref_id' => $refId,
                ]),
            ]);

            $reserve->balance = round((float) $reserve->balance + $amount, 8);
            $reserve->save();

            return $ledger;
        });
    }

    public function debit(float $amount, string $reason, ?string $refType = null, ?int $refId = null, ?int $adminId = null, string $currency = 'USDT'): ReserveLedger
    {
        return DB::transaction(function () use ($amount, $reason, $refType, $refId, $adminId, $currency) {
            $reserve = ReserveAccount::where('currency', $currency)->lockForUpdate()->first();
            if (!$reserve) {
                throw new RuntimeException('Reserve account not initialized.');
            }

            $balance = (float) $reserve->balance;
            if ($balance < $amount) {
                throw new RuntimeException('Insufficient reserve balance.');
            }

            $ledger = ReserveLedger::create([
                'reserve_account_id' => $reserve->id,
                'amount' => -$amount,
                'reason' => $reason,
                'created_by_admin_id' => $adminId,
                'meta' => array_filter([
                    'ref_type' => $refType,
                    'ref_id' => $refId,
                ]),
            ]);

            $reserve->balance = round($balance - $amount, 8);
            $reserve->save();

            return $ledger;
        });
    }
}
