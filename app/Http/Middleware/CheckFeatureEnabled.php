<?php

namespace App\Http\Middleware;

use App\Services\FeatureFlagService;
use Closure;
use Illuminate\Http\Request;

class CheckFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        $enabled = app(FeatureFlagService::class)->isEnabled($featureKey);

        if (!$enabled) {
            abort(404);
        }

        return $next($request);
    }
}
