<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'logo_path',
        'logo_light_path',
        'logo_dark_path',
        'favicon_path',
        'hero_headline',
        'hero_subtitle',
        'mobile',
        'email',
        'address',
        'description',
        'usdt_trc20_address',
        'min_deposit_usdt',
        'deposit_review_hours',
        'sellers_enabled',
        'nft_enabled',
        'bids_enabled',
        'reserve_enabled',
    ];
}
