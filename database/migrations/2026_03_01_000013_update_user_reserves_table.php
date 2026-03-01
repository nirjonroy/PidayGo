<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_reserves', function (Blueprint $table) {
            if (!Schema::hasColumn('user_reserves', 'level_id')) {
                $table->foreignId('level_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            }
            if (!Schema::hasColumn('user_reserves', 'reserve_plan_id')) {
                $table->foreignId('reserve_plan_id')->nullable()->constrained('reserve_plans')->nullOnDelete()->after('level_id');
            }
            if (!Schema::hasColumn('user_reserves', 'amount')) {
                $table->decimal('amount', 24, 8)->nullable()->after('reserve_plan_id');
            }
            if (!Schema::hasColumn('user_reserves', 'status')) {
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->after('amount');
            }
            if (!Schema::hasColumn('user_reserves', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('user_reserves', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('confirmed_at');
            }
            if (!Schema::hasColumn('user_reserves', 'meta')) {
                $table->json('meta')->nullable()->after('completed_at');
            }
        });

        // align reserved_balance precision if needed
        DB::statement('ALTER TABLE user_reserves MODIFY reserved_balance DECIMAL(24,8) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        Schema::table('user_reserves', function (Blueprint $table) {
            if (Schema::hasColumn('user_reserves', 'level_id')) {
                $table->dropForeign(['level_id']);
                $table->dropColumn('level_id');
            }
            if (Schema::hasColumn('user_reserves', 'reserve_plan_id')) {
                $table->dropForeign(['reserve_plan_id']);
                $table->dropColumn('reserve_plan_id');
            }
            if (Schema::hasColumn('user_reserves', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('user_reserves', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('user_reserves', 'confirmed_at')) {
                $table->dropColumn('confirmed_at');
            }
            if (Schema::hasColumn('user_reserves', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('user_reserves', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
