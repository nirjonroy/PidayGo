<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSettingService
{
    private const CACHE_KEY = 'site_settings.first';

    public function get(): ?SiteSetting
    {
        if (!Schema::hasTable('site_settings')) {
            return null;
        }

        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
            return SiteSetting::first();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
