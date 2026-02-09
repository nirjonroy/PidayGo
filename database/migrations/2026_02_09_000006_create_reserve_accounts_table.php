<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reserve_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('currency')->default('USDT');
            $table->decimal('balance', 18, 8)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reserve_accounts');
    }
};
