<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FeatureFlagService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $featureFlags = app(FeatureFlagService::class);
        $twoFactorEnabled = $featureFlags->isEnabled('two_factor_enabled');
        $postVerifyRoute = $twoFactorEnabled
            ? 'two-factor.setup'
            : ($featureFlags->isEnabled('kyc_enabled') ? 'kyc.form' : 'dashboard');

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($postVerifyRoute);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route($postVerifyRoute)->with('verified', true);
    }
}
