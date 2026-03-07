<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserReserve;
use App\Services\ReferralChainService;
use App\Services\MailSettingsService;
use App\Services\FeatureFlagService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create(Request $request, ?string $ref = null)
    {
        return view('auth.register', [
            'ref' => $ref ?: $request->query('ref'),
        ]);
    }

    public function store(Request $request, MailSettingsService $mailSettings, ReferralChainService $chainService, FeatureFlagService $featureFlags): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'ref_code' => ['required', 'string', 'max:20'],
        ]);

        $sponsor = User::where('ref_code', $validated['ref_code'])->first();
        if (!$sponsor) {
            return back()->withErrors(['ref_code' => 'Invalid referral code.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'ref_code' => $this->generateRefCode(),
        ]);

        $chainService->assignSponsorAndSlot($user, $sponsor->ref_code);
        UserReserve::firstOrCreate(['user_id' => $user->id], ['reserved_balance' => 0]);

        if ($mailSettings->isActive()) {
            event(new Registered($user));
        } else {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);
        $request->session()->put('two_factor_passed', false);
        $request->session()->put('login_at', time());

        if ($mailSettings->isActive()) {
            return redirect()->route('verification.notice');
        }

        if (!$featureFlags->isEnabled('two_factor_enabled')) {
            $request->session()->put('two_factor_passed', true);
            return redirect()->route('kyc.form');
        }

        return redirect()->route('two-factor.setup');
    }

    private function generateRefCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('ref_code', $code)->exists());

        return $code;
    }

}
