<?php

namespace App\Services;

use App\Models\WalletLedger;
use App\Models\User;

class WalletService
{
    public function addLedger(User $user, string $type, float $amount, ?string $referenceType = null, ?int $referenceId = null, array $meta = [], ?int $createdByAdminId = null): WalletLedger
    {
        return WalletLedger::create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'meta' => $meta,
            'created_by_admin_id' => $createdByAdminId,
        ]);
    }
}
