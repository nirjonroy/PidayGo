<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
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

        $setting = new SiteSetting($validated);

        if ($request->hasFile('logo')) {
            $setting->logo_path = $request->file('logo')->store('site', 'public');
        }

        $setting->save();
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

        $setting->fill($validated);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $request->file('logo')->store('site', 'public');
        }

        $setting->save();
        ActivityLog::record('site.settings.updated', $request->user('admin'), $setting);

        return redirect()->route('admin.site-settings.index')->with('status', 'Site settings updated.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'site_name' => ['required', 'string', 'max:150'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);
    }
}
