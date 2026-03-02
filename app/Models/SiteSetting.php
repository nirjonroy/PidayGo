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
        'two_factor_enabled',
        'footer_newsletter_title',
        'footer_newsletter_text',
        'footer_newsletter_placeholder',
        'footer_social_facebook',
        'footer_social_twitter',
        'footer_social_instagram',
        'footer_social_youtube',
        'footer_social_email',
        'footer_copyright_text',
    ];

    protected $casts = [
        'sellers_enabled' => 'boolean',
        'nft_enabled' => 'boolean',
        'bids_enabled' => 'boolean',
        'reserve_enabled' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];
}
