<?php

use App\Services\FeatureFlagService;

if (!function_exists('feature')) {
    function feature(string $key): bool
    {
        return app(FeatureFlagService::class)->isEnabled($key);
    }
}
