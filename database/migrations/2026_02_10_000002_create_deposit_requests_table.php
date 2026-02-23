<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deposit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency')->default('USDT');
            $table->string('chain')->default('TRC20');
            $table->string('to_address');
            $table->decimal('amount', 18, 8);
            $table->string('txid')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->foreignId('credited_ledger_id')->nullable()->constrained('wallet_ledgers')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index(['user_id', 'created_at']);
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposit_requests');
    }
};
