<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function store(Request $request, WalletService $walletService): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.0001'],
        ]);

        $amount = (float) $validated['amount'];
        $user = $request->user();
        $balance = (float) $user->walletLedgers()->sum('amount');

        if ($balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'requested_at' => now(),
            'eligible_at' => now()->addHours(72),
        ]);

        $walletService->debit(
            $user,
            'withdraw_request',
            $amount,
            [],
            $withdrawal
        );

        return back()->with('status', 'Withdrawal requested. Approval takes 72–96 hours.');
    }
}
