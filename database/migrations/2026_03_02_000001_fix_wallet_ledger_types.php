<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE wallet_ledgers MODIFY COLUMN type ENUM(
            'deposit',
            'stake_lock',
            'reward_credit',
            'ref_bonus',
            'withdraw_request',
            'withdraw_approved',
            'withdraw_rejected',
            'admin_adjust',
            'stake_unlocked',
            'reserve_deduct',
            'reserve_lock',
            'reserve_release',
            'sell_income',
            'nft_profit',
            'chain_income'
        ) NOT NULL");

        DB::statement("ALTER TABLE wallet_ledgers MODIFY COLUMN amount DECIMAL(24, 8) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_ledgers MODIFY COLUMN type ENUM(
            'deposit',
            'stake_lock',
            'reward_credit',
            'ref_bonus',
            'withdraw_request',
            'withdraw_approved',
            'withdraw_rejected',
            'admin_adjust',
            'stake_unlocked'
        ) NOT NULL");

        DB::statement("ALTER TABLE wallet_ledgers MODIFY COLUMN amount DECIMAL(18, 8) NOT NULL");
    }
};
