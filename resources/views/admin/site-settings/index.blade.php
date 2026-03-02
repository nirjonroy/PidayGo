@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Site Settings')

    @if (!$setting)
        <div class="alert alert-warning">No settings found. Create the first site settings record.</div>
        <a href="{{ route('admin.site-settings.create') }}" class="btn btn-primary">Create Settings</a>
    @else
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <strong>Site Name:</strong> {{ $setting->site_name }}
                </div>
                <div class="mb-3">
                    <strong>Logo:</strong>
                    @if ($setting->logo_path)
                        <div>
                            <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" style="height:60px;">
                        </div>
                    @else
                        <span class="text-secondary">Not set</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Logo Light:</strong>
                    @if ($setting->logo_light_path)
                        <div>
                            <img src="{{ asset('storage/' . $setting->logo_light_path) }}" alt="Logo Light" style="height:60px;">
                        </div>
                    @else
                        <span class="text-secondary">Not set</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Logo Dark:</strong>
                    @if ($setting->logo_dark_path)
                        <div>
                            <img src="{{ asset('storage/' . $setting->logo_dark_path) }}" alt="Logo Dark" style="height:60px;">
                        </div>
                    @else
                        <span class="text-secondary">Not set</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Favicon:</strong>
                    @if ($setting->favicon_path)
                        <div>
                            <img src="{{ asset('storage/' . $setting->favicon_path) }}" alt="Favicon" style="height:32px;">
                        </div>
                    @else
                        <span class="text-secondary">Not set</span>
                    @endif
                    <div class="mt-2">
                        <a href="{{ route('admin.site-settings.edit') }}" class="btn btn-sm btn-outline-primary">
                            Upload / Change Favicon
                        </a>
                    </div>
                </div>
                <div class="mb-3"><strong>Hero Headline:</strong> {{ $setting->hero_headline ?? '-' }}</div>
                <div class="mb-3"><strong>Hero Subtitle:</strong> {{ $setting->hero_subtitle ?? '-' }}</div>
                <div class="mb-3"><strong>Mobile:</strong> {{ $setting->mobile ?? '-' }}</div>
                <div class="mb-3"><strong>Email:</strong> {{ $setting->email ?? '-' }}</div>
                <div class="mb-3"><strong>Address:</strong> {{ $setting->address ?? '-' }}</div>
                <div class="mb-3"><strong>Description:</strong> {{ $setting->description ?? '-' }}</div>
                <div class="mb-3"><strong>Min Deposit (USDT):</strong> {{ $setting->min_deposit_usdt ?? 50 }}</div>
                <div class="mb-3"><strong>Deposit Review Hours:</strong> {{ $setting->deposit_review_hours ?? 24 }}</div>
                <div class="mb-3">
                    <strong>Theme Colors:</strong>
                    <div class="mt-1">
                        <div>Primary: {{ $setting->theme_primary_color ?? 'Default' }}</div>
                        <div>Secondary: {{ $setting->theme_secondary_color ?? 'Default' }}</div>
                    </div>
                </div>
                <div class="mb-3"><strong>Default Theme Mode:</strong> {{ ucfirst($setting->theme_mode ?? 'auto') }}</div>
                <div class="mb-3">
                    <strong>Feature Flags:</strong>
                    <div class="mt-1">
                        <span class="badge {{ $setting->sellers_enabled ? 'bg-success' : 'bg-secondary' }}">Sellers {{ $setting->sellers_enabled ? 'On' : 'Off' }}</span>
                        <span class="badge {{ $setting->nft_enabled ? 'bg-success' : 'bg-secondary' }}">NFTs {{ $setting->nft_enabled ? 'On' : 'Off' }}</span>
                        <span class="badge {{ $setting->bids_enabled ? 'bg-success' : 'bg-secondary' }}">Bids {{ $setting->bids_enabled ? 'On' : 'Off' }}</span>
                        <span class="badge {{ $setting->reserve_enabled ? 'bg-success' : 'bg-secondary' }}">Reserve {{ $setting->reserve_enabled ? 'On' : 'Off' }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Google Auth (2FA):</strong>
                    <span class="badge {{ ($setting->two_factor_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->two_factor_enabled ?? true) ? 'On' : 'Off' }}
                    </span>
                </div>

                <a href="{{ route('admin.site-settings.edit') }}" class="btn btn-primary">Edit Settings</a>
            </div>
        </div>
    @endif
@endsection
