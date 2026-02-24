<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SellersSeeder extends Seeder
{
    public function run(): void
    {
        if (Seller::query()->exists()) {
            return;
        }

        $faker = \Faker\Factory::create();
        $avatars = [
            'frontend/images/author/author-1.jpg',
            'frontend/images/author/author-2.jpg',
            'frontend/images/author/author-3.jpg',
            'frontend/images/author/author-4.jpg',
            'frontend/images/author/author-5.jpg',
            'frontend/images/author/author-6.jpg',
            'frontend/images/author/author-7.jpg',
            'frontend/images/author/author-8.jpg',
            'frontend/images/author/author-9.jpg',
            'frontend/images/author/author-10.jpg',
            'frontend/images/author/author-11.jpg',
            'frontend/images/author/author-12.jpg',
            'frontend/images/author/author-13.jpg',
            'frontend/images/author/author-14.jpg',
            'frontend/images/author/author-15.jpg',
        ];

        for ($i = 0; $i < 20; $i++) {
            $name = $faker->name();
            $usernameBase = Str::slug($name, '');
            $username = $usernameBase . $faker->unique()->numerify('###');

            Seller::create([
                'name' => $name,
                'username' => $username,
                'avatar_path' => $faker->randomElement($avatars),
                'volume' => $faker->randomFloat(4, 10, 20000),
                'is_verified' => $faker->boolean(40),
                'is_active' => true,
            ]);
        }
    }
}
