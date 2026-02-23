<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->enum('document_type', ['nid', 'passport', 'driving_license'])->nullable()->after('notes');
            $table->text('document_number')->nullable()->after('document_type');
        });
    }

    public function down()
    {
        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'document_number']);
        });
    }
};
