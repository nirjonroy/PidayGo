<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\NftItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class BidsSeeder extends Seeder
{
    public function run(): void
    {
        if (Bid::query()->exists()) {
            return;
        }

        $items = NftItem::query()->whereNotNull('auction_end_at')->get();
        $users = User::query()->get();
        $faker = \Faker\Factory::create();

        foreach ($items as $item) {
            $bidsCount = rand(1, 5);
            for ($i = 0; $i < $bidsCount; $i++) {
                $user = $users->isNotEmpty() ? $users->random() : null;
                $bidderName = $user ? null : $faker->name();

                Bid::create([
                    'nft_item_id' => $item->id,
                    'user_id' => $user?->id,
                    'bidder_name' => $bidderName,
                    'amount' => $faker->randomFloat(4, 10, 5000),
                ]);
            }
        }
    }
}
