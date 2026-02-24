<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('sellers_enabled')->default(true)->after('deposit_review_hours');
            $table->boolean('nft_enabled')->default(true)->after('sellers_enabled');
            $table->boolean('bids_enabled')->default(true)->after('nft_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['sellers_enabled', 'nft_enabled', 'bids_enabled']);
        });
    }
};
