<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\NotificationService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function store(Request $request, WalletService $walletService, NotificationService $notifications): RedirectResponse
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

        $notifications->notifyUser(
            $user->id,
            'withdraw_requested',
            'Withdrawal requested',
            'Your withdrawal request has been submitted and will be reviewed.',
            'info',
            ['withdrawal_request_id' => $withdrawal->id]
        );

        $notifications->notifyAdminsByRoleOrPermission(
            'withdrawal.review',
            'withdraw_requested',
            'New withdrawal request',
            'A new withdrawal request was submitted.',
            'warning',
            ['withdrawal_request_id' => $withdrawal->id]
        );

        return back()->with('status', 'Withdrawal requested. Approval takes 72–96 hours.');
    }
}
