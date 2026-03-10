<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use App\Services\FeatureFlagService;
use App\Services\SiteSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $setting = SiteSetting::first();

        return view('admin.site-settings.index', [
            'setting' => $setting,
        ]);
    }

    public function create()
    {
        if (SiteSetting::exists()) {
            return redirect()->route('admin.site-settings.index');
        }

        return view('admin.site-settings.form', [
            'setting' => new SiteSetting(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated = $this->normalizeThemeColors($validated);
        $validated['sellers_enabled'] = $request->boolean('sellers_enabled');
        $validated['nft_enabled'] = $request->boolean('nft_enabled');
        $validated['bids_enabled'] = $request->boolean('bids_enabled');
        $validated['reserve_enabled'] = $request->boolean('reserve_enabled');
        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');
        $validated['kyc_enabled'] = $request->boolean('kyc_enabled');

        $setting = new SiteSetting($validated);

        if ($request->hasFile('logo')) {
            $setting->logo_path = $request->file('logo')->store('site', 'public');
        }
        if ($request->hasFile('logo_light')) {
            $setting->logo_light_path = $request->file('logo_light')->store('site', 'public');
        }
        if ($request->hasFile('logo_dark')) {
            $setting->logo_dark_path = $request->file('logo_dark')->store('site', 'public');
        }
        if ($request->hasFile('favicon')) {
            $setting->favicon_path = $request->file('favicon')->store('site', 'public');
        }

        $setting->save();
        app(SiteSettingService::class)->clearCache();
        app(FeatureFlagService::class)->clearCache();
        ActivityLog::record('site.settings.created', $request->user('admin'), $setting);

        return redirect()->route('admin.site-settings.index')->with('status', 'Site settings saved.');
    }

    public function edit()
    {
        $setting = SiteSetting::firstOrFail();

        return view('admin.site-settings.form', [
            'setting' => $setting,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = SiteSetting::firstOrFail();
        $validated = $this->validatePayload($request);
        $validated = $this->normalizeThemeColors($validated);
        $validated['sellers_enabled'] = $request->boolean('sellers_enabled');
        $validated['nft_enabled'] = $request->boolean('nft_enabled');
        $validated['bids_enabled'] = $request->boolean('bids_enabled');
        $validated['reserve_enabled'] = $request->boolean('reserve_enabled');
        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');
        $validated['kyc_enabled'] = $request->boolean('kyc_enabled');

        $setting->fill($validated);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $request->file('logo')->store('site', 'public');
        }
        if ($request->hasFile('logo_light')) {
            if ($setting->logo_light_path) {
                Storage::disk('public')->delete($setting->logo_light_path);
            }
            $setting->logo_light_path = $request->file('logo_light')->store('site', 'public');
        }
        if ($request->hasFile('logo_dark')) {
            if ($setting->logo_dark_path) {
                Storage::disk('public')->delete($setting->logo_dark_path);
            }
            $setting->logo_dark_path = $request->file('logo_dark')->store('site', 'public');
        }
        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }
            $setting->favicon_path = $request->file('favicon')->store('site', 'public');
        }

        $setting->save();
        app(SiteSettingService::class)->clearCache();
        app(FeatureFlagService::class)->clearCache();
        ActivityLog::record('site.settings.updated', $request->user('admin'), $setting);

        return redirect()->route('admin.site-settings.index')->with('status', 'Site settings updated.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'site_name' => ['required', 'string', 'max:150'],
            'hero_headline' => ['nullable', 'string', 'max:200'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'footer_newsletter_title' => ['nullable', 'string', 'max:150'],
            'footer_newsletter_text' => ['nullable', 'string', 'max:2000'],
            'footer_newsletter_placeholder' => ['nullable', 'string', 'max:120'],
            'footer_social_facebook' => ['nullable', 'string', 'max:255'],
            'footer_social_twitter' => ['nullable', 'string', 'max:255'],
            'footer_social_instagram' => ['nullable', 'string', 'max:255'],
            'footer_social_youtube' => ['nullable', 'string', 'max:255'],
            'footer_social_email' => ['nullable', 'string', 'max:255'],
            'footer_copyright_text' => ['nullable', 'string', 'max:255'],
            'theme_primary_color' => ['nullable', 'string', 'regex:/^#?[0-9a-fA-F]{6}$/'],
            'theme_secondary_color' => ['nullable', 'string', 'regex:/^#?[0-9a-fA-F]{6}$/'],
            'theme_mode' => ['nullable', 'in:auto,light,dark'],
            'usdt_trc20_address' => ['nullable', 'string', 'max:120'],
            'min_deposit_usdt' => ['required', 'numeric', 'min:0'],
            'deposit_review_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'logo_light' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,ico', 'max:1024'],
            'sellers_enabled' => ['nullable', 'boolean'],
            'nft_enabled' => ['nullable', 'boolean'],
            'bids_enabled' => ['nullable', 'boolean'],
            'reserve_enabled' => ['nullable', 'boolean'],
            'two_factor_enabled' => ['nullable', 'boolean'],
            'kyc_enabled' => ['nullable', 'boolean'],
        ]);
    }

    private function normalizeThemeColors(array $validated): array
    {
        foreach (['theme_primary_color', 'theme_secondary_color'] as $field) {
            if (!empty($validated[$field])) {
                $color = ltrim($validated[$field], '#');
                $validated[$field] = '#' . strtoupper($color);
            }
        }

        return $validated;
    }
}
