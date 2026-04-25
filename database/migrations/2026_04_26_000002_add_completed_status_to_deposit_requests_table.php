<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE deposit_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'expired', 'Completed') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::table('deposit_requests')
            ->where('status', 'Completed')
            ->update(['status' => 'approved']);

        DB::statement("ALTER TABLE deposit_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'expired') NOT NULL DEFAULT 'pending'");
    }
};
