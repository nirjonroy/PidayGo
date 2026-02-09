<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reserve_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserve_account_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 18, 8);
            $table->string('reason');
            $table->foreignId('created_by_admin_id')->constrained('admins')->cascadeOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reserve_ledgers');
    }
};
