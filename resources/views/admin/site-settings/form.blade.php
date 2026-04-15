@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $setting->exists ? 'Edit Site Settings' : 'Create Site Settings')

    <div class="card">
        <div class="card-body">
            <form
                method="POST"
                action="{{ $setting->exists ? route('admin.site-settings.update') : route('admin.site-settings.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="site_name">Site Name</label>
                    <input id="site_name" name="site_name" class="form-control" value="{{ old('site_name', $setting->site_name) }}" required>
                    @error('site_name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="logo">Logo</label>
                    <input id="logo" name="logo" type="file" class="form-control">
                    @error('logo') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($setting->logo_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" style="height:60px;">
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label" for="logo_light">Logo Light</label>
                    <input id="logo_light" name="logo_light" type="file" class="form-control">
                    @error('logo_light') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($setting->logo_light_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $setting->logo_light_path) }}" alt="Logo Light" style="height:60px;">
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label" for="logo_dark">Logo Dark</label>
                    <input id="logo_dark" name="logo_dark" type="file" class="form-control">
                    @error('logo_dark') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($setting->logo_dark_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $setting->logo_dark_path) }}" alt="Logo Dark" style="height:60px;">
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label" for="favicon">Favicon</label>
                    <input id="favicon" name="favicon" type="file" class="form-control">
                    @error('favicon') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($setting->favicon_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $setting->favicon_path) }}" alt="Favicon" style="height:32px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="hero_headline">Hero Headline</label>
                    <input id="hero_headline" name="hero_headline" class="form-control" value="{{ old('hero_headline', $setting->hero_headline) }}">
                    @error('hero_headline') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="hero_subtitle">Hero Subtitle</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                    @error('hero_subtitle') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="mobile">Mobile</label>
                    <input id="mobile" name="mobile" class="form-control" value="{{ old('mobile', $setting->mobile) }}">
                    @error('mobile') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $setting->email) }}">
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <input id="address" name="address" class="form-control" value="{{ old('address', $setting->address) }}">
                    @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $setting->description) }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <h5 class="mb-2">Footer Settings</h5>
                    <div class="mb-3">
                        <label class="form-label" for="footer_newsletter_title">Newsletter Title</label>
                        <input id="footer_newsletter_title" name="footer_newsletter_title" class="form-control" value="{{ old('footer_newsletter_title', $setting->footer_newsletter_title) }}">
                        @error('footer_newsletter_title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="footer_newsletter_text">Newsletter Text</label>
                        <textarea id="footer_newsletter_text" name="footer_newsletter_text" class="form-control" rows="3">{{ old('footer_newsletter_text', $setting->footer_newsletter_text) }}</textarea>
                        @error('footer_newsletter_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="footer_newsletter_placeholder">Newsletter Placeholder</label>
                        <input id="footer_newsletter_placeholder" name="footer_newsletter_placeholder" class="form-control" value="{{ old('footer_newsletter_placeholder', $setting->footer_newsletter_placeholder) }}">
                        @error('footer_newsletter_placeholder') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="footer_social_facebook">Facebook URL</label>
                            <input id="footer_social_facebook" name="footer_social_facebook" class="form-control" value="{{ old('footer_social_facebook', $setting->footer_social_facebook) }}">
                            @error('footer_social_facebook') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="footer_social_twitter">Twitter URL</label>
                            <input id="footer_social_twitter" name="footer_social_twitter" class="form-control" value="{{ old('footer_social_twitter', $setting->footer_social_twitter) }}">
                            @error('footer_social_twitter') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="footer_social_instagram">Instagram URL</label>
                            <input id="footer_social_instagram" name="footer_social_instagram" class="form-control" value="{{ old('footer_social_instagram', $setting->footer_social_instagram) }}">
                            @error('footer_social_instagram') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="footer_social_youtube">YouTube URL</label>
                            <input id="footer_social_youtube" name="footer_social_youtube" class="form-control" value="{{ old('footer_social_youtube', $setting->footer_social_youtube) }}">
                            @error('footer_social_youtube') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="footer_social_email">Email (mailto)</label>
                            <input id="footer_social_email" name="footer_social_email" class="form-control" value="{{ old('footer_social_email', $setting->footer_social_email) }}">
                            @error('footer_social_email') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="footer_copyright_text">Copyright Text</label>
                            <input id="footer_copyright_text" name="footer_copyright_text" class="form-control" value="{{ old('footer_copyright_text', $setting->footer_copyright_text) }}">
                            @error('footer_copyright_text') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="mb-2">Theme Colors</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="theme_primary_color">Primary Color</label>
                            <input id="theme_primary_color" name="theme_primary_color" class="form-control" placeholder="#0B0814" value="{{ old('theme_primary_color', $setting->theme_primary_color) }}">
                            <div class="form-text">Leave blank to use the default theme color.</div>
                            @error('theme_primary_color') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="theme_secondary_color">Secondary (Accent) Color</label>
                            <input id="theme_secondary_color" name="theme_secondary_color" class="form-control" placeholder="#F5B04C" value="{{ old('theme_secondary_color', $setting->theme_secondary_color) }}">
                            <div class="form-text">Leave blank to use the default accent color.</div>
                            @error('theme_secondary_color') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="theme_mode">Default Theme Mode</label>
                        <select id="theme_mode" name="theme_mode" class="form-select">
                            @php
                                $currentThemeMode = old('theme_mode', $setting->theme_mode ?? 'auto');
                            @endphp
                            <option value="auto" @selected($currentThemeMode === 'auto')>Auto (use user preference)</option>
                            <option value="light" @selected($currentThemeMode === 'light')>Light</option>
                            <option value="dark" @selected($currentThemeMode === 'dark')>Dark</option>
                        </select>
                        <div class="form-text">Applies when a user has not selected a theme yet.</div>
                        @error('theme_mode') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="min_deposit_usdt">Minimum Deposit (USDT)</label>
                    <input id="min_deposit_usdt" name="min_deposit_usdt" type="number" step="0.0001" class="form-control" value="{{ old('min_deposit_usdt', $setting->min_deposit_usdt ?? 50) }}" required>
                    @error('min_deposit_usdt') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="deposit_review_hours">Deposit Review Hours</label>
                    <input id="deposit_review_hours" name="deposit_review_hours" type="number" min="1" max="168" class="form-control" value="{{ old('deposit_review_hours', $setting->deposit_review_hours ?? 24) }}" required>
                    @error('deposit_review_hours') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <h5 class="mb-2">Menu Labels</h5>
                    <div class="form-text mb-3">Leave any field blank to keep the default menu name.</div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_home_label">Header: Home</label>
                            <input id="nav_home_label" name="nav_home_label" class="form-control" value="{{ old('nav_home_label', $setting->nav_home_label) }}">
                            @error('nav_home_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_explore_label">Header: Explore</label>
                            <input id="nav_explore_label" name="nav_explore_label" class="form-control" value="{{ old('nav_explore_label', $setting->nav_explore_label) }}">
                            @error('nav_explore_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_rankings_label">Header: Rankings</label>
                            <input id="nav_rankings_label" name="nav_rankings_label" class="form-control" value="{{ old('nav_rankings_label', $setting->nav_rankings_label) }}">
                            @error('nav_rankings_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_marketplace_label">Header: Marketplace</label>
                            <input id="nav_marketplace_label" name="nav_marketplace_label" class="form-control" value="{{ old('nav_marketplace_label', $setting->nav_marketplace_label) }}">
                            @error('nav_marketplace_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_profile_label">Dropdown Trigger: Profile</label>
                            <input id="nav_profile_label" name="nav_profile_label" class="form-control" value="{{ old('nav_profile_label', $setting->nav_profile_label) }}">
                            @error('nav_profile_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_dashboard_label">Dropdown: Dashboard</label>
                            <input id="nav_dashboard_label" name="nav_dashboard_label" class="form-control" value="{{ old('nav_dashboard_label', $setting->nav_dashboard_label) }}">
                            @error('nav_dashboard_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_wallet_label">Wallet Label</label>
                            <input id="nav_wallet_label" name="nav_wallet_label" class="form-control" value="{{ old('nav_wallet_label', $setting->nav_wallet_label) }}">
                            @error('nav_wallet_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_deposit_label">Deposit Label</label>
                            <input id="nav_deposit_label" name="nav_deposit_label" class="form-control" value="{{ old('nav_deposit_label', $setting->nav_deposit_label) }}">
                            @error('nav_deposit_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_withdrawals_label">Withdrawals Label</label>
                            <input id="nav_withdrawals_label" name="nav_withdrawals_label" class="form-control" value="{{ old('nav_withdrawals_label', $setting->nav_withdrawals_label) }}">
                            @error('nav_withdrawals_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_stake_label">Stake Label</label>
                            <input id="nav_stake_label" name="nav_stake_label" class="form-control" value="{{ old('nav_stake_label', $setting->nav_stake_label) }}">
                            @error('nav_stake_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_reserve_label">Reserve Label</label>
                            <input id="nav_reserve_label" name="nav_reserve_label" class="form-control" value="{{ old('nav_reserve_label', $setting->nav_reserve_label) }}">
                            @error('nav_reserve_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_notifications_label">Notifications Label</label>
                            <input id="nav_notifications_label" name="nav_notifications_label" class="form-control" value="{{ old('nav_notifications_label', $setting->nav_notifications_label) }}">
                            @error('nav_notifications_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_support_label">Support Label</label>
                            <input id="nav_support_label" name="nav_support_label" class="form-control" value="{{ old('nav_support_label', $setting->nav_support_label) }}">
                            @error('nav_support_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_profile_settings_label">Profile Settings Label</label>
                            <input id="nav_profile_settings_label" name="nav_profile_settings_label" class="form-control" value="{{ old('nav_profile_settings_label', $setting->nav_profile_settings_label) }}">
                            @error('nav_profile_settings_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_login_label">Login Label</label>
                            <input id="nav_login_label" name="nav_login_label" class="form-control" value="{{ old('nav_login_label', $setting->nav_login_label) }}">
                            @error('nav_login_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_register_label">Register Label</label>
                            <input id="nav_register_label" name="nav_register_label" class="form-control" value="{{ old('nav_register_label', $setting->nav_register_label) }}">
                            @error('nav_register_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_logout_label">Logout Label</label>
                            <input id="nav_logout_label" name="nav_logout_label" class="form-control" value="{{ old('nav_logout_label', $setting->nav_logout_label) }}">
                            @error('nav_logout_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_mobile_dashboard_label">Mobile Quick Nav: Dashboard</label>
                            <input id="nav_mobile_dashboard_label" name="nav_mobile_dashboard_label" class="form-control" value="{{ old('nav_mobile_dashboard_label', $setting->nav_mobile_dashboard_label) }}">
                            @error('nav_mobile_dashboard_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_mobile_marketplace_label">Mobile Quick Nav: Marketplace</label>
                            <input id="nav_mobile_marketplace_label" name="nav_mobile_marketplace_label" class="form-control" value="{{ old('nav_mobile_marketplace_label', $setting->nav_mobile_marketplace_label) }}">
                            @error('nav_mobile_marketplace_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_mobile_reserve_label">Mobile Quick Nav: Reserve</label>
                            <input id="nav_mobile_reserve_label" name="nav_mobile_reserve_label" class="form-control" value="{{ old('nav_mobile_reserve_label', $setting->nav_mobile_reserve_label) }}">
                            @error('nav_mobile_reserve_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_mobile_stake_label">Mobile Quick Nav: Stake</label>
                            <input id="nav_mobile_stake_label" name="nav_mobile_stake_label" class="form-control" value="{{ old('nav_mobile_stake_label', $setting->nav_mobile_stake_label) }}">
                            @error('nav_mobile_stake_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="nav_mobile_wallet_label">Mobile Quick Nav: Wallet</label>
                            <input id="nav_mobile_wallet_label" name="nav_mobile_wallet_label" class="form-control" value="{{ old('nav_mobile_wallet_label', $setting->nav_mobile_wallet_label) }}">
                            @error('nav_mobile_wallet_label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="mb-2">Feature Settings</h5>
                    <div class="form-check mb-2">
                        <input type="hidden" name="sellers_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="sellers_enabled" name="sellers_enabled" value="1" {{ old('sellers_enabled', $setting->sellers_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sellers_enabled">Enable Sellers</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="nft_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="nft_enabled" name="nft_enabled" value="1" {{ old('nft_enabled', $setting->nft_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="nft_enabled">Enable PI/Explore</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="bids_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="bids_enabled" name="bids_enabled" value="1" {{ old('bids_enabled', $setting->bids_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="bids_enabled">Enable Bids</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="reserve_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="reserve_enabled" name="reserve_enabled" value="1" {{ old('reserve_enabled', $setting->reserve_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="reserve_enabled">Enable Reserve</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="two_factor_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="two_factor_enabled" name="two_factor_enabled" value="1" {{ old('two_factor_enabled', $setting->two_factor_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="two_factor_enabled">Enable Google Auth (2FA)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="kyc_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="kyc_enabled" name="kyc_enabled" value="1" {{ old('kyc_enabled', $setting->kyc_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="kyc_enabled">Require KYC for users</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.site-settings.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection

