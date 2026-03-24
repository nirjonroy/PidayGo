<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            if (!Schema::hasColumn('levels', 'chain_income_a_percent')) {
                $table->decimal('chain_income_a_percent', 6, 3)->nullable()->after('income_max_percent');
            }
            if (!Schema::hasColumn('levels', 'chain_income_b_percent')) {
                $table->decimal('chain_income_b_percent', 6, 3)->nullable()->after('chain_income_a_percent');
            }
            if (!Schema::hasColumn('levels', 'chain_income_c_percent')) {
                $table->decimal('chain_income_c_percent', 6, 3)->nullable()->after('chain_income_b_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            if (Schema::hasColumn('levels', 'chain_income_c_percent')) {
                $table->dropColumn('chain_income_c_percent');
            }
            if (Schema::hasColumn('levels', 'chain_income_b_percent')) {
                $table->dropColumn('chain_income_b_percent');
            }
            if (Schema::hasColumn('levels', 'chain_income_a_percent')) {
                $table->dropColumn('chain_income_a_percent');
            }
        });
    }
};
