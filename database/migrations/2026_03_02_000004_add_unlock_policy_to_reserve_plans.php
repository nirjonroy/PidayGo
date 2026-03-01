<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reserve_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('reserve_plans', 'unlock_policy')) {
                $table->enum('unlock_policy', ['never', 'after_sells', 'manual'])->default('never')->after('max_sells');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reserve_plans', function (Blueprint $table) {
            if (Schema::hasColumn('reserve_plans', 'unlock_policy')) {
                $table->dropColumn('unlock_policy');
            }
        });
    }
};
