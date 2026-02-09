<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ReserveAccount;
use App\Models\ReserveLedger;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $admin = $request->user('admin');
        $error = null;

        DB::transaction(function () use ($withdrawal, $walletService, $admin, &$error) {
            $lockedWithdrawal = WithdrawalRequest::whereKey($withdrawal->id)->lockForUpdate()->first();

            if (!$lockedWithdrawal || $lockedWithdrawal->status !== 'pending') {
                $error = 'Already processed.';
                return;
            }

            $reserve = ReserveAccount::where('currency', 'USDT')->lockForUpdate()->first();
            if (!$reserve) {
                $reserve = ReserveAccount::create([
                    'currency' => 'USDT',
                    'balance' => 0,
                ]);
            }

            $amount = (float) $lockedWithdrawal->amount;
            $reserveBalance = (float) $reserve->balance;

            if ($reserveBalance < $amount) {
                $error = 'Not enough reserve balance to approve this withdrawal.';
                return;
            }

            ReserveLedger::create([
                'reserve_account_id' => $reserve->id,
                'amount' => -$amount,
                'reason' => 'withdrawal_approved',
                'created_by_admin_id' => $admin->id,
                'meta' => [
                    'withdrawal_id' => $lockedWithdrawal->id,
                    'user_id' => $lockedWithdrawal->user_id,
                ],
            ]);

            $reserve->balance = round($reserveBalance - $amount, 8);
            $reserve->save();

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

        return back()->with('status', 'Withdrawal rejected.');
    }
}
