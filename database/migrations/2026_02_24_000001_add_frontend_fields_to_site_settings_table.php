<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('logo_light_path')->nullable()->after('logo_path');
            $table->string('logo_dark_path')->nullable()->after('logo_light_path');
            $table->string('favicon_path')->nullable()->after('logo_dark_path');
            $table->string('hero_headline')->nullable()->after('favicon_path');
            $table->text('hero_subtitle')->nullable()->after('hero_headline');
        });
    }

    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_light_path',
                'logo_dark_path',
                'favicon_path',
                'hero_headline',
                'hero_subtitle',
            ]);
        });
    }
};
