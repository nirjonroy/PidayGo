<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class FeatureFlagService
{
    private const CACHE_KEY = 'feature_flags';

    public function __construct(private SiteSettingService $settings)
    {
    }

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
            $setting = $this->settings->get();

            return [
                'sellers_enabled' => (bool) ($setting?->sellers_enabled ?? true),
                'nft_enabled' => (bool) ($setting?->nft_enabled ?? true),
                'bids_enabled' => (bool) ($setting?->bids_enabled ?? true),
                'reserve_enabled' => (bool) ($setting?->reserve_enabled ?? true),
                'two_factor_enabled' => (bool) ($setting?->two_factor_enabled ?? true),
            ];
        });
    }

    public function isEnabled(string $key): bool
    {
        $flags = $this->all();
        return $flags[$key] ?? true;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
