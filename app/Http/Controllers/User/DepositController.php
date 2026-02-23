<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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

        $address = $setting?->usdt_trc20_address;
        $minDeposit = (float) ($setting?->min_deposit_usdt ?? 50);
        $reviewHours = (int) ($setting?->deposit_review_hours ?? 24);

        return view('wallet.deposit', [
            'address' => $address,
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

        $address = $setting?->usdt_trc20_address;
        $minDeposit = (float) ($setting?->min_deposit_usdt ?? 50);
        $reviewHours = (int) ($setting?->deposit_review_hours ?? 24);

        if (empty($address)) {
            return back()->withErrors(['address' => 'Deposit address is not configured.'])->withInput();
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minDeposit],
            'txid' => ['required', 'regex:/^[a-fA-F0-9]{64}$/', 'unique:deposit_requests,txid'],
        ]);

        DepositRequest::create([
            'user_id' => $request->user()->id,
            'currency' => 'USDT',
            'chain' => 'TRC20',
            'to_address' => $address,
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
