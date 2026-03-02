<?php

namespace App\Providers;

use App\Services\FeatureFlagService;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.frontend', function ($view) {
            if (!Schema::hasTable('site_settings')) {
                $view->with([
                    'siteSetting' => null,
                    'settings' => null,
                    'featureFlags' => [
                        'sellers_enabled' => true,
                        'nft_enabled' => true,
                        'bids_enabled' => true,
                        'two_factor_enabled' => true,
                    ],
                ]);
                return;
            }

            $setting = app(SiteSettingService::class)->get();
            $featureFlags = app(FeatureFlagService::class)->all();
            $view->with([
                'siteSetting' => $setting,
                'settings' => $setting,
                'featureFlags' => $featureFlags,
            ]);
        });
    }
}
