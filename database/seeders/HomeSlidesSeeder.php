<?php

namespace Database\Seeders;

use App\Models\HomeSlide;
use Illuminate\Database\Seeder;

class HomeSlidesSeeder extends Seeder
{
    public function run(): void
    {
        if (HomeSlide::query()->exists()) {
            return;
        }

        $slides = [
            [
                'title' => 'Discover Rare PI',
                'subtitle' => 'Curated collections updated weekly.',
                'button_text' => 'Explore',
                'button_url' => '/explore',
                'image_path' => 'frontend/images/carousel/crs-12.jpg',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'title' => 'Trade & Earn Daily',
                'subtitle' => 'Stake and grow your holdings.',
                'button_text' => 'Stake Now',
                'button_url' => '/stake',
                'image_path' => 'frontend/images/carousel/crs-13.jpg',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Top Creators',
                'subtitle' => 'Meet verified artists worldwide.',
                'button_text' => 'View Sellers',
                'button_url' => '/explore',
                'image_path' => 'frontend/images/carousel/crs-14.jpg',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            HomeSlide::create($slide);
        }
    }
}

