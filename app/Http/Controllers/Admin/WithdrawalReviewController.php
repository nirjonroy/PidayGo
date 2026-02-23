<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WithdrawalRequest;
use App\Services\NotificationService;
use App\Services\ReserveService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalReviewController extends Controller
{
    public function index()
    {
        $status = request('status', 'pending');

        $query = WithdrawalRequest::query()
            ->with([
                'user.profile',
                'user.bankAccounts',
                'user.latestKycRequest',
            ])
            ->orderByDesc('requested_at');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.withdrawals.index', [
            'requests' => $query->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function approve(Request $request, WithdrawalRequest $withdrawal, WalletService $walletService, NotificationService $notifications, ReserveService $reserveService): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['withdrawal' => 'Already processed.']);
        }

        if (now()->lt($withdrawal->eligible_at)) {
            return back()->withErrors(['withdrawal' => 'Approval allowed after 72 hours.']);
        }

        $admin = $request->user('admin');
        $error = null;

        DB::transaction(function () use ($withdrawal, $walletService, $admin, &$error, $reserveService) {
            $lockedWithdrawal = WithdrawalRequest::whereKey($withdrawal->id)->lockForUpdate()->first();

            if (!$lockedWithdrawal || $lockedWithdrawal->status !== 'pending') {
                $error = 'Already processed.';
                return;
            }

            $amount = (float) $lockedWithdrawal->amount;
            try {
                $reserveService->debit($amount, 'withdrawal_approved', 'withdrawal', $lockedWithdrawal->id, $admin->id);
            } catch (\RuntimeException $exception) {
                $error = $exception->getMessage();
                return;
            }

            $walletService->debit(
                $lockedWithdrawal->user,
                'withdraw_approved',
                $amount,
                [],
                $lockedWithdrawal,
                $admin
            );

            $lockedWithdrawal->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $admin->id,
            ]);

            ActivityLog::record('withdrawal.approved', $admin, $lockedWithdrawal);
        });

        if ($error) {
            return back()->withErrors(['withdrawal' => $error]);
        }

        $notifications->notifyUser(
            $withdrawal->user_id,
            'withdraw_approved',
            'Withdrawal approved',
            'Your withdrawal has been approved.',
            'success',
            ['withdrawal_request_id' => $withdrawal->id],
            true
        );

        return back()->with('status', 'Withdrawal approved.');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal, WalletService $walletService, NotificationService $notifications): RedirectResponse
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

        $walletService->credit(
            $withdrawal->user,
            'withdraw_rejected',
            (float) $withdrawal->amount,
            ['reason' => 'Rejected'],
            $withdrawal,
            $request->user('admin')
        );

        ActivityLog::record('withdrawal.rejected', $request->user('admin'), $withdrawal, [
            'notes' => $validated['notes'] ?? null,
        ]);

        $notifications->notifyUser(
            $withdrawal->user_id,
            'withdraw_rejected',
            'Withdrawal rejected',
            $validated['notes'] ?? 'Your withdrawal was rejected.',
            'error',
            ['withdrawal_request_id' => $withdrawal->id]
        );

        return back()->with('status', 'Withdrawal rejected.');
    }
}
