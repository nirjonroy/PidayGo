<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('nft_sales', 'nft_item_id')) {
                $table->foreignId('nft_item_id')->nullable()->after('user_reserve_id')->constrained('nft_items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            if (Schema::hasColumn('nft_sales', 'nft_item_id')) {
                $table->dropForeign(['nft_item_id']);
                $table->dropColumn('nft_item_id');
            }
        });
    }
};
