<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nft_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image_path');
            $table->text('description')->nullable();
            $table->foreignId('creator_seller_id')->nullable()->constrained('sellers')->nullOnDelete();
            $table->foreignId('owner_seller_id')->nullable()->constrained('sellers')->nullOnDelete();
            $table->decimal('price', 18, 8)->nullable();
            $table->dateTime('auction_end_at')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nft_items');
    }
};
