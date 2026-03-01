<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reserve_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('reserve_plans', 'max_sells')) {
                $table->unsignedInteger('max_sells')->nullable()->after('profit_max_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reserve_plans', function (Blueprint $table) {
            if (Schema::hasColumn('reserve_plans', 'max_sells')) {
                $table->dropColumn('max_sells');
            }
        });
    }
};
