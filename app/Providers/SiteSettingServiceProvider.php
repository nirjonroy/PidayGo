<?php

namespace App\Providers;

use App\Models\FooterLink;
use App\Services\FeatureFlagService;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SiteSettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            if (!Schema::hasTable('site_settings')) {
                $view->with([
                    'siteName' => 'PidayGo',
                    'siteLogo' => null,
                    'siteMobile' => null,
                    'siteEmail' => null,
                    'siteAddress' => null,
                    'siteDescription' => null,
                    'siteUsdtTrc20Address' => null,
                    'siteMinDepositUsdt' => 50,
                    'siteDepositReviewHours' => 24,
                    'featureFlags' => [
                        'sellers_enabled' => true,
                        'nft_enabled' => true,
                        'bids_enabled' => true,
                        'reserve_enabled' => true,
                        'two_factor_enabled' => true,
                        'kyc_enabled' => true,
                    ],
                    'footerLinks' => collect(),
                ]);
                return;
            }

            $setting = app(SiteSettingService::class)->get();
            $featureFlags = app(FeatureFlagService::class)->all();

            $footerLinks = collect();
            if (Schema::hasTable('footer_links')) {
                $footerLinks = FooterLink::active()
                    ->orderBy('section')
                    ->orderBy('sort_order')
                    ->get()
                    ->groupBy('section');
            }

            $view->with([
                'siteName' => $setting?->site_name ?? 'PidayGo',
                'siteLogo' => $setting?->logo_path,
                'siteMobile' => $setting?->mobile,
                'siteEmail' => $setting?->email,
                'siteAddress' => $setting?->address,
                'siteDescription' => $setting?->description,
                'siteUsdtTrc20Address' => $setting?->usdt_trc20_address,
                'siteMinDepositUsdt' => $setting?->min_deposit_usdt ?? 50,
                'siteDepositReviewHours' => $setting?->deposit_review_hours ?? 24,
                'featureFlags' => $featureFlags,
                'footerLinks' => $footerLinks,
            ]);
        });
    }
}
