<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reserve_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('reserve_plans', 'wallet_balance_min')) {
                $table->decimal('wallet_balance_min', 24, 8)->nullable()->after('level_id');
            }

            if (!Schema::hasColumn('reserve_plans', 'wallet_balance_max')) {
                $table->decimal('wallet_balance_max', 24, 8)->nullable()->after('wallet_balance_min');
            }
        });

        $levels = DB::table('levels')->get()->keyBy('id');

        DB::table('reserve_plans')->orderBy('id')->get()->each(function ($plan) use ($levels) {
            $level = $levels->get($plan->level_id);

            $depositMin = (float) ($level->min_deposit ?? 0);
            $depositMax = (float) ($level->max_deposit ?? 0);
            $reservationMin = (float) ($level->min_reservation ?? 0);
            $reservationMax = (float) ($level->max_reservation ?? 0);

            $rangeMin = ($depositMin > 0 || $depositMax > 0) ? $depositMin : $reservationMin;
            $rangeMax = ($depositMin > 0 || $depositMax > 0) ? $depositMax : $reservationMax;
            $rangeMax = $rangeMax > 0 ? $rangeMax : $rangeMin;

            DB::table('reserve_plans')
                ->where('id', $plan->id)
                ->update([
                    'wallet_balance_min' => $rangeMin,
                    'wallet_balance_max' => $rangeMax,
                ]);
        });

        try {
            Schema::table('reserve_plans', function (Blueprint $table) {
                $table->dropUnique('reserve_plans_level_id_reserve_amount_unique');
            });
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        try {
            Schema::table('reserve_plans', function (Blueprint $table) {
                $table->unique(['level_id', 'reserve_amount']);
            });
        } catch (\Throwable $e) {
        }

        Schema::table('reserve_plans', function (Blueprint $table) {
            if (Schema::hasColumn('reserve_plans', 'wallet_balance_max')) {
                $table->dropColumn('wallet_balance_max');
            }

            if (Schema::hasColumn('reserve_plans', 'wallet_balance_min')) {
                $table->dropColumn('wallet_balance_min');
            }
        });
    }
};
