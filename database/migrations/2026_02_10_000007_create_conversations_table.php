<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->enum('status', ['open', 'pending', 'answered', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->timestamp('last_message_at')->nullable();
            $table->enum('last_message_by', ['user', 'admin'])->nullable();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'last_message_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
