<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chain_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nft_sale_id')->constrained('nft_sales')->cascadeOnDelete();
            $table->unsignedInteger('level_depth');
            $table->decimal('percent', 8, 3);
            $table->decimal('amount', 24, 8);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chain_commissions');
    }
};
