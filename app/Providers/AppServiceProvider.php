<?php

namespace App\Providers;

use App\Models\SiteSetting;
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
                ]);
                return;
            }

            $setting = SiteSetting::first();
            $view->with([
                'siteSetting' => $setting,
                'settings' => $setting,
            ]);
        });
    }
}
