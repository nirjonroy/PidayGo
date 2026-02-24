<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('system_alerts')->default(true);
            $table->boolean('item_sold')->default(true);
            $table->boolean('auction_expiration')->default(true);
            $table->boolean('bid_activity')->default(true);
            $table->boolean('outbid')->default(true);
            $table->boolean('price_change')->default(true);
            $table->boolean('successful_purchase')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_settings');
    }
};
