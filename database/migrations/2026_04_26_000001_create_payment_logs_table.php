<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->default('oxapay');
            $table->foreignId('deposit_request_id')->nullable()->constrained('deposit_requests')->nullOnDelete();
            $table->string('track_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('status')->nullable();
            $table->string('signature')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->string('message')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->longText('raw_body')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'track_id']);
            $table->index(['gateway', 'order_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_logs');
    }
};
