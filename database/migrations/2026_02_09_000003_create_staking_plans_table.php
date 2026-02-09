<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stake_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min_amount', 18, 8)->default(0);
            $table->decimal('max_amount', 18, 8)->nullable();
            $table->decimal('daily_rate', 10, 6);
            $table->unsignedInteger('duration_days');
            $table->decimal('max_payout_multiplier', 6, 2)->default(2.00);
            $table->unsignedInteger('level_required')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stake_plans');
    }
};
