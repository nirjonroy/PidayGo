<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserve_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->decimal('reserve_amount', 24, 8);
            $table->decimal('profit_min_percent', 8, 3);
            $table->decimal('profit_max_percent', 8, 3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['level_id', 'reserve_amount']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserve_plans');
    }
};
