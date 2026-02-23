<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DepositAddress;
use App\Models\DepositRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function create(Request $request): View
    {
        $setting = SiteSetting::first();
        $activeAddress = DepositAddress::where('is_active', true)->first();
        $minDeposit = (float) ($setting?->min_deposit_usdt ?? 50);
        $reviewHours = (int) ($setting?->deposit_review_hours ?? 24);

        return view('wallet.deposit', [
            'address' => $activeAddress?->address,
            'qrPayload' => $activeAddress?->qr_payload ?? $activeAddress?->address,
            'currency' => $activeAddress?->currency ?? 'USDT',
            'chain' => $activeAddress?->chain ?? 'TRC20',
            'minDeposit' => $minDeposit,
            'reviewHours' => $reviewHours,
            'history' => $request->user()
                ->depositRequests()
                ->orderByDesc('id')
                ->limit(20)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $setting = SiteSetting::first();
        $activeAddress = DepositAddress::where('is_active', true)->first();
        $minDeposit = (float) ($setting?->min_deposit_usdt ?? 50);
        $reviewHours = (int) ($setting?->deposit_review_hours ?? 24);

        if (!$activeAddress) {
            return back()->withErrors(['address' => 'Deposit address is not configured.'])->withInput();
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minDeposit],
            'txid' => ['required', 'regex:/^[a-fA-F0-9]{64}$/', 'unique:deposit_requests,txid'],
        ]);

        DepositRequest::create([
            'user_id' => $request->user()->id,
            'currency' => $activeAddress->currency,
            'chain' => $activeAddress->chain,
            'to_address' => $activeAddress->address,
            'amount' => $validated['amount'],
            'txid' => $validated['txid'],
            'status' => 'pending',
            'expires_at' => now()->addHours($reviewHours),
        ]);

        return redirect()->route('wallet.deposit')->with(
            'status',
            'Deposit submitted. It will be reviewed within ' . $reviewHours . ' hours.'
        );
    }
}
