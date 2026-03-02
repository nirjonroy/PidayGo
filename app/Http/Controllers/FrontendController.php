<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\HomeSlide;
use App\Models\NftItem;
use App\Models\Seller;
use App\Models\SiteSetting;
use App\Models\StakePlan;
use App\Models\Bid;
use App\Services\FeatureFlagService;
use App\Services\SiteSettingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $siteSetting = app(SiteSettingService::class)->get();
        $features = app(FeatureFlagService::class);
        $sellersEnabled = $features->isEnabled('sellers_enabled');
        $nftEnabled = $features->isEnabled('nft_enabled');
        $bidsEnabled = $features->isEnabled('bids_enabled');

        return view('frontend.home', [
            'siteSetting' => $siteSetting,
            'settings' => $siteSetting,
            'slides' => HomeSlide::where('is_active', true)->orderBy('sort_order')->get(),
            'topSellers' => $sellersEnabled
                ? Seller::where('is_active', true)->orderByDesc('volume')->limit(15)->get()
                : collect(),
            'trendingItems' => $nftEnabled
                ? NftItem::where('status', 'published')
                    ->where('is_active', true)
                    ->where('is_trending', true)
                    ->with(['creatorSeller'])
                    ->orderByDesc('id')
                    ->limit(12)
                    ->get()
                : collect(),
            'latestBids' => $bidsEnabled
                ? Bid::where('is_active', true)
                    ->with(['item', 'user'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                : collect(),
            'plans' => StakePlan::where('is_active', true)->orderBy('min_amount')->get(),
            'heroHeadline' => $siteSetting?->hero_headline,
            'heroSubtitle' => $siteSetting?->hero_subtitle,
            'latestPosts' => BlogPost::visible()
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
            'featureFlags' => [
                'sellers_enabled' => $sellersEnabled,
                'nft_enabled' => $nftEnabled,
                'bids_enabled' => $bidsEnabled,
            ],
        ]);
    }

    public function explore(Request $request): View
    {
        $items = NftItem::where('status', 'published')
            ->where('is_active', true)
            ->with(['creatorSeller'])
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.explore', [
            'items' => $items,
        ]);
    }

    public function itemDetails(string $slug): View
    {
        $features = app(FeatureFlagService::class);

        $item = NftItem::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'creatorSeller',
                'ownerSeller',
                'bids' => function ($query) use ($features) {
                    if (!$features->isEnabled('bids_enabled')) {
                        $query->whereRaw('1=0');
                        return;
                    }
                    $query->where('is_active', true)->with('user')->orderByDesc('amount')->limit(10);
                },
            ])
            ->firstOrFail();

        return view('frontend.item-details', [
            'item' => $item,
            'featureFlags' => [
                'bids_enabled' => $features->isEnabled('bids_enabled'),
            ],
        ]);
    }
}
