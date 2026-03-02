<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'theme_mode')) {
                $table->string('theme_mode', 20)->nullable()->after('theme_secondary_color');
            }
        });
    }

    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'theme_mode')) {
                $table->dropColumn('theme_mode');
            }
        });
    }
};
