<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nft_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_reserve_id')->constrained('user_reserves')->cascadeOnDelete();
            $table->decimal('sale_amount', 24, 8);
            $table->decimal('profit_percent', 8, 3);
            $table->decimal('profit_amount', 24, 8);
            $table->enum('status', ['submitted', 'approved', 'rejected', 'paid'])->default('paid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nft_sales');
    }
};
