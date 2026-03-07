<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\MailSettingsService;
use App\Services\FeatureFlagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, MailSettingsService $mailSettings, FeatureFlagService $featureFlags): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->put('two_factor_passed', false);
        $request->session()->put('login_at', time());

        $user = $request->user();

        if ($mailSettings->isActive() && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (!$featureFlags->isEnabled('two_factor_enabled')) {
            $request->session()->put('two_factor_passed', true);
            return redirect()->intended('/dashboard');
        }

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup');
        }

        return redirect()->route('two-factor.challenge');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
