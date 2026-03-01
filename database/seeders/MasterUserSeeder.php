<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        $master = User::where('ref_code', 'MASTER')->orWhere('is_master', true)->first();

        if ($master) {
            $master->update([
                'is_master' => true,
                'ref_code' => 'MASTER',
            ]);
            return;
        }

        User::create([
            'name' => 'Master',
            'email' => 'master@pidaygo.com',
            'password' => Hash::make(Str::random(24)),
            'ref_code' => 'MASTER',
            'is_master' => true,
        ]);
    }
}
