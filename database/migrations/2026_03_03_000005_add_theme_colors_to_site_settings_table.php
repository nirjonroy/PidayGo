<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'theme_primary_color')) {
                $table->string('theme_primary_color', 20)->nullable()->after('footer_copyright_text');
            }
            if (!Schema::hasColumn('site_settings', 'theme_secondary_color')) {
                $table->string('theme_secondary_color', 20)->nullable()->after('theme_primary_color');
            }
        });
    }

    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'theme_secondary_color')) {
                $table->dropColumn('theme_secondary_color');
            }
            if (Schema::hasColumn('site_settings', 'theme_primary_color')) {
                $table->dropColumn('theme_primary_color');
            }
        });
    }
};
