<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DepositRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $query = DepositRequest::query()->with('user')->orderByDesc('id');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.deposits.index', [
            'requests' => $query->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function show(DepositRequest $deposit): View
    {
        return view('admin.deposits.show', [
            'deposit' => $deposit->load(['user', 'reviewedBy', 'creditedLedger']),
        ]);
    }

    public function approve(Request $request, DepositRequest $deposit, WalletService $walletService): RedirectResponse
    {
        $admin = $request->user('admin');
        $error = null;

        DB::transaction(function () use ($deposit, $walletService, $admin, &$error) {
            $locked = DepositRequest::whereKey($deposit->id)->lockForUpdate()->first();

            if (!$locked || $locked->status !== 'pending') {
                $error = 'Already processed.';
                return;
            }

            if ($locked->expires_at && now()->gt($locked->expires_at)) {
                $locked->update([
                    'status' => 'expired',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now(),
                    'admin_note' => 'Expired before approval.',
                ]);
                $error = 'Deposit has expired.';
                return;
            }

            if ($locked->credited_ledger_id) {
                $error = 'Deposit already credited.';
                return;
            }

            $ledger = $walletService->credit(
                $locked->user,
                'deposit',
                $locked->amount,
                [
                    'txid' => $locked->txid,
                    'chain' => $locked->chain,
                    'currency' => $locked->currency,
                    'to_address' => $locked->to_address,
                    'deposit_request_id' => $locked->id,
                ],
                $locked,
                $admin
            );

            $locked->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'credited_ledger_id' => $ledger->id,
            ]);

            ActivityLog::record('deposit.approved', $admin, $locked);
        });

        if ($error) {
            return back()->withErrors(['deposit' => $error]);
        }

        return back()->with('status', 'Deposit approved.');
    }

    public function reject(Request $request, DepositRequest $deposit): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'max:1000'],
        ]);

        if ($deposit->status !== 'pending') {
            return back()->withErrors(['deposit' => 'Already processed.']);
        }

        $deposit->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user('admin')->id,
            'reviewed_at' => now(),
            'admin_note' => $validated['admin_note'],
        ]);

        ActivityLog::record('deposit.rejected', $request->user('admin'), $deposit, [
            'note' => $validated['admin_note'],
        ]);

        return back()->with('status', 'Deposit rejected.');
    }

    public function expire(Request $request, DepositRequest $deposit): RedirectResponse
    {
        if ($deposit->status !== 'pending') {
            return back()->withErrors(['deposit' => 'Already processed.']);
        }

        if ($deposit->expires_at && now()->lte($deposit->expires_at)) {
            return back()->withErrors(['deposit' => 'Deposit is not expired yet.']);
        }

        $deposit->update([
            'status' => 'expired',
            'reviewed_by' => $request->user('admin')->id,
            'reviewed_at' => now(),
            'admin_note' => 'Expired manually.',
        ]);

        ActivityLog::record('deposit.expired', $request->user('admin'), $deposit);

        return back()->with('status', 'Deposit marked as expired.');
    }
}
