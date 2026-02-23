<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('usdt_trc20_address')->nullable()->after('description');
            $table->decimal('min_deposit_usdt', 18, 8)->default(50)->after('usdt_trc20_address');
            $table->unsignedInteger('deposit_review_hours')->default(24)->after('min_deposit_usdt');
        });
    }

    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['usdt_trc20_address', 'min_deposit_usdt', 'deposit_review_hours']);
        });
    }
};
