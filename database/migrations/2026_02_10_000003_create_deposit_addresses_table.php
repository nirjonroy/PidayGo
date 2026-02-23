<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deposit_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('currency')->default('USDT');
            $table->string('chain')->default('TRC20');
            $table->string('address')->unique();
            $table->string('qr_payload')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposit_addresses');
    }
};
