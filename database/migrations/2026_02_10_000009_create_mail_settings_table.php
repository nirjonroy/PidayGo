<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);

            $table->string('primary_host')->nullable();
            $table->unsignedInteger('primary_port')->nullable();
            $table->string('primary_username')->nullable();
            $table->text('primary_password_encrypted')->nullable();
            $table->string('primary_encryption')->nullable();
            $table->string('primary_from_address')->nullable();
            $table->string('primary_from_name')->nullable();

            $table->string('secondary_host')->nullable();
            $table->unsignedInteger('secondary_port')->nullable();
            $table->string('secondary_username')->nullable();
            $table->text('secondary_password_encrypted')->nullable();
            $table->string('secondary_encryption')->nullable();
            $table->string('secondary_from_address')->nullable();
            $table->string('secondary_from_name')->nullable();

            $table->enum('verification_mailer', ['primary', 'secondary'])->default('primary');
            $table->enum('notification_mailer', ['primary', 'secondary'])->default('primary');

            $table->text('admin_notify_emails')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mail_settings');
    }
};
