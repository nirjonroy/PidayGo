<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_code', 30)->unique()->nullable()->after('id');
        });

        $userIds = DB::table('users')->whereNull('user_code')->pluck('id');
        foreach ($userIds as $userId) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['user_code' => $this->generateUserCode()]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['user_code']);
            $table->dropColumn('user_code');
        });
    }

    private function generateUserCode(): string
    {
        do {
            $code = 'PG' . now()->format('ymd') . strtoupper(Str::random(6));
        } while (DB::table('users')->where('user_code', $code)->exists());

        return $code;
    }
};
