<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            if (!Schema::hasColumn('levels', 'min_deposit')) {
                $table->decimal('min_deposit', 24, 8)->default(0)->after('code');
            }
            if (!Schema::hasColumn('levels', 'max_deposit')) {
                $table->decimal('max_deposit', 24, 8)->default(0)->after('min_deposit');
            }
            if (!Schema::hasColumn('levels', 'req_chain_a')) {
                $table->unsignedInteger('req_chain_a')->default(0)->after('max_deposit');
            }
            if (!Schema::hasColumn('levels', 'req_chain_b')) {
                $table->unsignedInteger('req_chain_b')->default(0)->after('req_chain_a');
            }
            if (!Schema::hasColumn('levels', 'req_chain_c')) {
                $table->unsignedInteger('req_chain_c')->default(0)->after('req_chain_b');
            }
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            if (Schema::hasColumn('levels', 'min_deposit')) {
                $table->dropColumn('min_deposit');
            }
            if (Schema::hasColumn('levels', 'max_deposit')) {
                $table->dropColumn('max_deposit');
            }
            if (Schema::hasColumn('levels', 'req_chain_a')) {
                $table->dropColumn('req_chain_a');
            }
            if (Schema::hasColumn('levels', 'req_chain_b')) {
                $table->dropColumn('req_chain_b');
            }
            if (Schema::hasColumn('levels', 'req_chain_c')) {
                $table->dropColumn('req_chain_c');
            }
        });
    }
};
