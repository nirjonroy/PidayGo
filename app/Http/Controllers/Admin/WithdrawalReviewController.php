<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WithdrawalReviewController extends Controller
{
    public function index()
    {
        $status = request('status', 'pending');

        $query = WithdrawalRequest::query()->with('user')->orderByDesc('requested_at');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.withdrawals.index', [
            'requests' => $query->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function approve(Request $request, WithdrawalRequest $withdrawal, WalletService $walletService): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['withdrawal' => 'Already processed.']);
        }

        if (now()->lt($withdrawal->eligible_at)) {
            return back()->withErrors(['withdrawal' => 'Approval allowed after 72 hours.']);
        }

        $withdrawal->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user('admin')->id,
        ]);

        // Optional ledger entry for audit (no balance change).
        $walletService->addLedger(
            $withdrawal->user,
            'withdraw_approved',
            0,
            WithdrawalRequest::class,
            $withdrawal->id,
            [],
            $request->user('admin')->id
        );

        ActivityLog::record('withdrawal.approved', $request->user('admin'), $withdrawal);

        return back()->with('status', 'Withdrawal approved.');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal, WalletService $walletService): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['withdrawal' => 'Already processed.']);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user('admin')->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        $walletService->addLedger(
            $withdrawal->user,
            'withdraw_rejected',
            (float) $withdrawal->amount,
            WithdrawalRequest::class,
            $withdrawal->id,
            ['reason' => 'Rejected'],
            $request->user('admin')->id
        );

        ActivityLog::record('withdrawal.rejected', $request->user('admin'), $withdrawal, [
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', 'Withdrawal rejected.');
    }
}
