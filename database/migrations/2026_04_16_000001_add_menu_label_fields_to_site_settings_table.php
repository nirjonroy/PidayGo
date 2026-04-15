<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $columns = [
                'nav_home_label',
                'nav_explore_label',
                'nav_rankings_label',
                'nav_marketplace_label',
                'nav_profile_label',
                'nav_dashboard_label',
                'nav_wallet_label',
                'nav_deposit_label',
                'nav_withdrawals_label',
                'nav_stake_label',
                'nav_reserve_label',
                'nav_notifications_label',
                'nav_support_label',
                'nav_profile_settings_label',
                'nav_login_label',
                'nav_register_label',
                'nav_logout_label',
                'nav_mobile_dashboard_label',
                'nav_mobile_marketplace_label',
                'nav_mobile_reserve_label',
                'nav_mobile_stake_label',
                'nav_mobile_wallet_label',
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('site_settings', $column)) {
                    $table->string($column, 80)->nullable()->after('theme_mode');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $columns = [
                'nav_mobile_wallet_label',
                'nav_mobile_stake_label',
                'nav_mobile_reserve_label',
                'nav_mobile_marketplace_label',
                'nav_mobile_dashboard_label',
                'nav_logout_label',
                'nav_register_label',
                'nav_login_label',
                'nav_profile_settings_label',
                'nav_support_label',
                'nav_notifications_label',
                'nav_reserve_label',
                'nav_stake_label',
                'nav_withdrawals_label',
                'nav_deposit_label',
                'nav_wallet_label',
                'nav_dashboard_label',
                'nav_profile_label',
                'nav_marketplace_label',
                'nav_rankings_label',
                'nav_explore_label',
                'nav_home_label',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
