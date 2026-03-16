<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserve_plan_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserve_plan_id')->constrained('reserve_plans')->cascadeOnDelete();
            $table->decimal('wallet_balance_min', 24, 8);
            $table->decimal('wallet_balance_max', 24, 8);
            $table->decimal('reserve_percentage', 8, 3);
            $table->timestamps();

            $table->index(['reserve_plan_id', 'wallet_balance_min'], 'reserve_plan_ranges_plan_balance_index');
        });

        if (!Schema::hasTable('reserve_plans')) {
            return;
        }

        $plans = DB::table('reserve_plans')->orderBy('id')->get();
        foreach ($plans as $plan) {
            $walletMin = (float) ($plan->wallet_balance_min ?? 0);
            $walletMax = (float) ($plan->wallet_balance_max ?? 0);
            $reservePercentage = (float) ($plan->reserve_amount ?? 0);

            if ($walletMin <= 0 && $walletMax <= 0 && $reservePercentage <= 0) {
                continue;
            }

            DB::table('reserve_plan_ranges')->insert([
                'reserve_plan_id' => $plan->id,
                'wallet_balance_min' => $walletMin,
                'wallet_balance_max' => $walletMax > 0 ? $walletMax : $walletMin,
                'reserve_percentage' => $reservePercentage,
                'created_at' => $plan->created_at ?? now(),
                'updated_at' => $plan->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reserve_plan_ranges');
    }
};
