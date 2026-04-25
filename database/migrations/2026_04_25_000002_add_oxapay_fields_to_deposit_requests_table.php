<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE deposit_requests MODIFY txid VARCHAR(255) NULL');

        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('credited_ledger_id');
            $table->string('gateway_order_id')->nullable()->after('gateway');
            $table->string('gateway_track_id')->nullable()->after('gateway_order_id');
            $table->text('gateway_payment_url')->nullable()->after('gateway_track_id');
            $table->text('gateway_qr_code')->nullable()->after('gateway_payment_url');
            $table->decimal('pay_amount', 24, 8)->nullable()->after('gateway_qr_code');
            $table->string('pay_currency', 20)->nullable()->after('pay_amount');
            $table->json('gateway_payload')->nullable()->after('pay_currency');

            $table->index('gateway_order_id');
            $table->index('gateway_track_id');
        });
    }

    public function down()
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->dropIndex(['gateway_order_id']);
            $table->dropIndex(['gateway_track_id']);
            $table->dropColumn([
                'gateway',
                'gateway_order_id',
                'gateway_track_id',
                'gateway_payment_url',
                'gateway_qr_code',
                'pay_amount',
                'pay_currency',
                'gateway_payload',
            ]);
        });

        DB::statement("ALTER TABLE deposit_requests MODIFY txid VARCHAR(255) NOT NULL");
    }
};
