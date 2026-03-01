<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ref_code')) {
                $table->string('ref_code', 20)->unique()->after('user_code');
            }
            if (!Schema::hasColumn('users', 'referred_by_id')) {
                $table->unsignedBigInteger('referred_by_id')->nullable()->after('ref_code');
                $table->foreign('referred_by_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'chain_slot')) {
                $table->enum('chain_slot', ['A', 'B', 'C'])->nullable()->after('referred_by_id');
            }
            if (!Schema::hasColumn('users', 'chain_path')) {
                $table->string('chain_path')->nullable()->after('chain_slot');
            }
            if (!Schema::hasColumn('users', 'is_master')) {
                $table->boolean('is_master')->default(false)->after('chain_path');
            }
        });

        // Backfill missing ref_code
        $users = DB::table('users')->whereNull('ref_code')->get(['id']);
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'ref_code' => $this->generateRefCode(),
            ]);
        }

        // Ensure master has chain_path
        $master = DB::table('users')->where('is_master', true)->first();
        if ($master && empty($master->chain_path)) {
            DB::table('users')->where('id', $master->id)->update([
                'chain_path' => $master->id . '/',
            ]);
        }

        // Make ref_code non-null if possible
        if (Schema::hasColumn('users', 'ref_code')) {
            DB::statement("ALTER TABLE users MODIFY ref_code VARCHAR(20) NOT NULL");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referred_by_id')) {
                $table->dropForeign(['referred_by_id']);
                $table->dropColumn('referred_by_id');
            }
            if (Schema::hasColumn('users', 'chain_slot')) {
                $table->dropColumn('chain_slot');
            }
            if (Schema::hasColumn('users', 'chain_path')) {
                $table->dropColumn('chain_path');
            }
            if (Schema::hasColumn('users', 'is_master')) {
                $table->dropColumn('is_master');
            }
        });
    }

    private function generateRefCode(): string
    {
        do {
            $code = 'PG' . strtoupper(Str::random(6));
        } while (DB::table('users')->where('ref_code', $code)->exists());

        return $code;
    }
};
