<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'deposit',
                'stake_lock',
                'reward_credit',
                'ref_bonus',
                'withdraw_request',
                'withdraw_approved',
                'withdraw_rejected',
                'admin_adjust',
                'stake_unlocked',
            ]);
            $table->decimal('amount', 18, 8);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_ledgers');
    }
};
