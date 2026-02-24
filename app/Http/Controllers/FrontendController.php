<?php

namespace App\Http\Controllers;

use App\Models\HomeSlide;
use App\Models\NftItem;
use App\Models\Seller;
use App\Models\SiteSetting;
use App\Models\StakePlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $siteSetting = SiteSetting::first();

        return view('frontend.home', [
            'siteSetting' => $siteSetting,
            'settings' => $siteSetting,
            'slides' => HomeSlide::where('is_active', true)->orderBy('sort_order')->get(),
            'topSellers' => Seller::where('is_active', true)->orderByDesc('volume')->limit(15)->get(),
            'trendingItems' => NftItem::where('status', 'published')
                ->where('is_trending', true)
                ->with(['creatorSeller'])
                ->orderByDesc('id')
                ->limit(12)
                ->get(),
            'plans' => StakePlan::where('is_active', true)->orderBy('min_amount')->get(),
            'heroHeadline' => $siteSetting?->hero_headline,
            'heroSubtitle' => $siteSetting?->hero_subtitle,
        ]);
    }

    public function explore(Request $request): View
    {
        $items = NftItem::where('status', 'published')
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
        $item = NftItem::where('slug', $slug)
            ->with([
                'creatorSeller',
                'ownerSeller',
                'bids' => function ($query) {
                    $query->with('user')->orderByDesc('amount')->limit(10);
                },
            ])
            ->firstOrFail();

        return view('frontend.item-details', [
            'item' => $item,
        ]);
    }
}
