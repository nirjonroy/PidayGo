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
            if (!Schema::hasColumn('user_reserves', 'sell_available_at')) {
                $table->timestamp('sell_available_at')->nullable()->after('confirmed_at');
            }
        });

        DB::table('user_reserves')
            ->whereNotNull('confirmed_at')
            ->whereNull('sell_available_at')
            ->update([
                'sell_available_at' => DB::raw('confirmed_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('user_reserves', function (Blueprint $table) {
            if (Schema::hasColumn('user_reserves', 'sell_available_at')) {
                $table->dropColumn('sell_available_at');
            }
        });
    }
};
