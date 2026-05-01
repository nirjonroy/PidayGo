<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\GatewaySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    private const GATEWAY_NAME = 'oxapay';

    public function index(): View
    {
        $settings = GatewaySetting::firstOrNew([
            'gateway_name' => self::GATEWAY_NAME,
        ]);

        return view('admin.payment-settings.index', [
            'settings' => $settings,
            'hasApiKey' => filled($settings->getRawOriginal('api_key')),
            'hasSecretKey' => filled($settings->getRawOriginal('secret_key')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'is_active' => ['nullable', 'boolean'],
            'api_key' => ['nullable', 'string', 'max:2000'],
            'secret_key' => ['nullable', 'string', 'max:2000'],
        ])->validate();

        $settings = GatewaySetting::firstOrNew([
            'gateway_name' => self::GATEWAY_NAME,
        ]);

        $willHaveApiKey = $request->filled('api_key') || filled($settings->getRawOriginal('api_key'));

        if ($request->boolean('is_active') && !$willHaveApiKey) {
            throw ValidationException::withMessages([
                'api_key' => 'Merchant API key is required before activating OxaPay.',
            ]);
        }

        $settings->gateway_name = self::GATEWAY_NAME;
        $settings->is_active = (bool) ($validated['is_active'] ?? false);

        if ($request->filled('api_key')) {
            $settings->api_key = $validated['api_key'];
        }

        if ($request->filled('secret_key')) {
            $settings->secret_key = $validated['secret_key'];
        }

        $settings->save();

        ActivityLog::record('payment.settings.updated', $request->user('admin'), $settings, [
            'gateway_name' => self::GATEWAY_NAME,
            'is_active' => $settings->is_active,
        ]);

        return redirect()->route('admin.payment-settings.index')->with('status', 'Payment settings saved.');
    }
}
