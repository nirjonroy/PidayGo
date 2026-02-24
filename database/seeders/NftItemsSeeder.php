<?php

namespace Database\Seeders;

use App\Models\NftItem;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NftItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (NftItem::query()->exists()) {
            return;
        }

        $faker = \Faker\Factory::create();
        $sellers = Seller::query()->get();

        if ($sellers->isEmpty()) {
            $this->call(SellersSeeder::class);
            $sellers = Seller::query()->get();
        }

        $images = [
            'frontend/images/items/static-21.jpg',
            'frontend/images/items/static-22.jpg',
            'frontend/images/items/static-23.jpg',
            'frontend/images/items/static-24.jpg',
            'frontend/images/items/anim-9.webp',
            'frontend/images/items/anim-10.webp',
        ];

        for ($i = 0; $i < 40; $i++) {
            $title = $faker->unique()->words(3, true);
            $slug = Str::slug($title) . '-' . Str::lower(Str::random(6));
            $creator = $sellers->random();
            $owner = $sellers->random();
            $isAuction = $faker->boolean(40);

            NftItem::create([
                'title' => Str::title($title),
                'slug' => $slug,
                'image_path' => $faker->randomElement($images),
                'description' => $faker->sentence(18),
                'creator_seller_id' => $creator->id,
                'owner_seller_id' => $owner->id,
                'price' => $faker->randomFloat(4, 50, 5000),
                'auction_end_at' => $isAuction ? now()->addDays(rand(1, 20)) : null,
                'likes_count' => $faker->numberBetween(0, 500),
                'views_count' => $faker->numberBetween(10, 10000),
                'is_trending' => false,
                'is_featured' => $faker->boolean(25),
                'status' => 'published',
                'is_active' => true,
            ]);
        }

        $trendingIds = NftItem::query()->inRandomOrder()->limit(10)->pluck('id');
        if ($trendingIds->isNotEmpty()) {
            NftItem::query()->whereIn('id', $trendingIds)->update(['is_trending' => true]);
        }
    }
}
