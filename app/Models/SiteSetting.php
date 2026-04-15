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
        'kyc_enabled',
        'footer_newsletter_title',
        'footer_newsletter_text',
        'footer_newsletter_placeholder',
        'footer_social_facebook',
        'footer_social_twitter',
        'footer_social_instagram',
        'footer_social_youtube',
        'footer_social_email',
        'footer_copyright_text',
        'theme_primary_color',
        'theme_secondary_color',
        'theme_mode',
        'nav_home_label',
        'nav_explore_label',
        'nav_rankings_label',
        'nav_marketplace_label',
        'nav_profile_label',
        'nav_dashboard_label',
        'nav_wallet_label',
        'nav_deposit_label',
        'nav_withdrawals_label',
        'nav_stake_label',
        'nav_reserve_label',
        'nav_notifications_label',
        'nav_support_label',
        'nav_profile_settings_label',
        'nav_login_label',
        'nav_register_label',
        'nav_logout_label',
        'nav_mobile_dashboard_label',
        'nav_mobile_marketplace_label',
        'nav_mobile_reserve_label',
        'nav_mobile_stake_label',
        'nav_mobile_wallet_label',
    ];

    protected $casts = [
        'sellers_enabled' => 'boolean',
        'nft_enabled' => 'boolean',
        'bids_enabled' => 'boolean',
        'reserve_enabled' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'kyc_enabled' => 'boolean',
    ];
}
