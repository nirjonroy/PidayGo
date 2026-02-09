<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stake_plan_id')->constrained('stake_plans')->cascadeOnDelete();
            $table->decimal('principal_amount', 18, 8);
            $table->enum('status', ['active', 'paused', 'completed', 'canceled'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('last_reward_at')->nullable();
            $table->decimal('total_reward_paid', 18, 8)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stakes');
    }
};
