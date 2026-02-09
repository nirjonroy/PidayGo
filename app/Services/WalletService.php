<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use App\Models\WalletLedger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    /**
     * Get current balance by summing all ledger amounts.
     */
    public function getBalance(User $user): string
    {
        return (string) $user->walletLedgers()->sum('amount');
    }

    /**
     * Credit a user's wallet (positive amount).
     *
     * Example:
     *   // $walletService->credit($user, 'deposit', '100.00', ['source' => 'manual'], null, $admin);
     */
    public function credit(User $user, string $type, float|string $amount, array $meta = [], ?Model $reference = null, ?Admin $admin = null): WalletLedger
    {
        return DB::transaction(function () use ($user, $type, $amount, $meta, $reference, $admin) {
            $value = $this->normalizeAmount($amount);

            return $this->createLedger($user, $type, $value, $meta, $reference, $admin);
        });
    }

    /**
     * Debit a user's wallet (negative amount). Throws if balance is insufficient.
     *
     * Example:
     *   // $walletService->debit($user, 'stake_lock', '25.00', ['plan_id' => 1], $stake);
     */
    public function debit(User $user, string $type, float|string $amount, array $meta = [], ?Model $reference = null, ?Admin $admin = null): WalletLedger
    {
        return DB::transaction(function () use ($user, $type, $amount, $meta, $reference, $admin) {
            $value = $this->normalizeAmount($amount);
            $balance = (float) $this->getBalance($user);

            if ($balance < (float) $value) {
                throw new RuntimeException('Insufficient balance.');
            }

            return $this->createLedger($user, $type, -$value, $meta, $reference, $admin);
        });
    }

    private function createLedger(User $user, string $type, float $amount, array $meta, ?Model $reference, ?Admin $admin): WalletLedger
    {
        return WalletLedger::create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
            'meta' => $meta,
            'created_by_admin_id' => $admin?->id,
        ]);
    }

    private function normalizeAmount(float|string $amount): float
    {
        return (float) $amount;
    }
}
