<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'footer_newsletter_title')) {
                $table->string('footer_newsletter_title', 150)->nullable()->after('hero_subtitle');
            }
            if (!Schema::hasColumn('site_settings', 'footer_newsletter_text')) {
                $table->text('footer_newsletter_text')->nullable()->after('footer_newsletter_title');
            }
            if (!Schema::hasColumn('site_settings', 'footer_newsletter_placeholder')) {
                $table->string('footer_newsletter_placeholder', 120)->nullable()->after('footer_newsletter_text');
            }
            if (!Schema::hasColumn('site_settings', 'footer_social_facebook')) {
                $table->string('footer_social_facebook', 255)->nullable()->after('footer_newsletter_placeholder');
            }
            if (!Schema::hasColumn('site_settings', 'footer_social_twitter')) {
                $table->string('footer_social_twitter', 255)->nullable()->after('footer_social_facebook');
            }
            if (!Schema::hasColumn('site_settings', 'footer_social_instagram')) {
                $table->string('footer_social_instagram', 255)->nullable()->after('footer_social_twitter');
            }
            if (!Schema::hasColumn('site_settings', 'footer_social_youtube')) {
                $table->string('footer_social_youtube', 255)->nullable()->after('footer_social_instagram');
            }
            if (!Schema::hasColumn('site_settings', 'footer_social_email')) {
                $table->string('footer_social_email', 255)->nullable()->after('footer_social_youtube');
            }
            if (!Schema::hasColumn('site_settings', 'footer_copyright_text')) {
                $table->string('footer_copyright_text', 255)->nullable()->after('footer_social_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $columns = [
                'footer_newsletter_title',
                'footer_newsletter_text',
                'footer_newsletter_placeholder',
                'footer_social_facebook',
                'footer_social_twitter',
                'footer_social_instagram',
                'footer_social_youtube',
                'footer_social_email',
                'footer_copyright_text',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
