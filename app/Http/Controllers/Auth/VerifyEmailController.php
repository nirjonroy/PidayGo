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
        $twoFactorEnabled = app(FeatureFlagService::class)->isEnabled('two_factor_enabled');

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($twoFactorEnabled ? 'two-factor.setup' : 'kyc.form');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route($twoFactorEnabled ? 'two-factor.setup' : 'kyc.form')->with('verified', true);
    }
}
