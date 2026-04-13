<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_bank_accounts', function (Blueprint $table) {
            $table->string('network', 30)->nullable()->after('user_id');
            $table->text('wallet_address')->nullable()->after('network');
            $table->string('address_label', 120)->nullable()->after('wallet_address');
            $table->string('memo_tag', 120)->nullable()->after('address_label');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE user_bank_accounts MODIFY bank_name VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY account_name VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY account_number TEXT NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY branch VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY routing_number VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY swift_code VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY ifsc_code VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY currency VARCHAR(255) NULL');
        }
    }

    public function down()
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE user_bank_accounts MODIFY bank_name VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY account_name VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY account_number TEXT NOT NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY branch VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY routing_number VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY swift_code VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY ifsc_code VARCHAR(255) NULL');
            DB::statement('ALTER TABLE user_bank_accounts MODIFY currency VARCHAR(255) NULL');
        }

        Schema::table('user_bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['network', 'wallet_address', 'address_label', 'memo_tag']);
        });
    }
};
