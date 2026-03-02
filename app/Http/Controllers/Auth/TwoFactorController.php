<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FeatureFlagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request)
    {
        if (!app(FeatureFlagService::class)->isEnabled('two_factor_enabled')) {
            return redirect()->route('kyc.form');
        }

        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (!$user->two_factor_secret) {
            $user->forceFill([
                'two_factor_secret' => $google2fa->generateSecretKey(),
            ])->save();
        }

        return view('auth.two-factor-setup', [
            'secret' => $user->two_factor_secret,
            'qrInline' => $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->two_factor_secret
            ),
        ]);
    }

    public function storeSetup(Request $request): RedirectResponse
    {
        if (!app(FeatureFlagService::class)->isEnabled('two_factor_enabled')) {
            return redirect()->route('kyc.form');
        }

        $request->validate([
            'one_time_password' => ['required', 'string'],
        ]);

        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($user->two_factor_secret, $request->input('one_time_password'))) {
            return back()->withErrors([
                'one_time_password' => 'The provided code is invalid.',
            ]);
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->put('two_factor_passed', true);

        return redirect()->route('kyc.form');
    }

    public function showChallenge()
    {
        if (!app(FeatureFlagService::class)->isEnabled('two_factor_enabled')) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        if (!app(FeatureFlagService::class)->isEnabled('two_factor_enabled')) {
            return redirect()->intended('/dashboard');
        }

        $request->validate([
            'one_time_password' => ['required', 'string'],
        ]);

        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($user->two_factor_secret, $request->input('one_time_password'))) {
            return back()->withErrors([
                'one_time_password' => 'The provided code is invalid.',
            ]);
        }

        $request->session()->put('two_factor_passed', true);

        return redirect()->intended('/dashboard');
    }
}
