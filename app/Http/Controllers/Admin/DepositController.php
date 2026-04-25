<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DepositRequest;
use App\Services\NotificationService;
use App\Services\OxaPayService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
            'oxapayConnection' => app(OxaPayService::class)->connectionStatus(),
        ]);
    }

    public function show(DepositRequest $deposit): View
    {
        return view('admin.deposits.show', [
            'deposit' => $deposit->load(['user', 'reviewedBy', 'creditedLedger']),
        ]);
    }

    public function syncOxaPay(
        Request $request,
        DepositRequest $deposit,
        OxaPayService $oxaPay,
        WalletService $walletService,
        NotificationService $notifications
    ): RedirectResponse {
        if ($deposit->gateway !== 'oxapay' || empty($deposit->gateway_track_id)) {
            return back()->withErrors(['deposit' => 'This deposit does not have an OxaPay track ID.']);
        }

        try {
            $response = $oxaPay->paymentStatus($deposit);
        } catch (\Throwable $e) {
            return back()->withErrors(['deposit' => $e->getMessage()]);
        }

        $data = (array) ($response['data'] ?? []);
        $gatewayStatus = Str::lower((string) ($data['status'] ?? ''));
        $admin = $request->user('admin');
        $credited = false;
        $expired = false;
        $message = 'OxaPay status synced: ' . ($data['status'] ?? 'unknown') . '.';

        DB::transaction(function () use ($deposit, $data, $response, $gatewayStatus, $walletService, $admin, &$credited, &$expired) {
            $locked = DepositRequest::query()
                ->whereKey($deposit->id)
                ->lockForUpdate()
                ->first();

            if (!$locked) {
                return;
            }

            $txid = $this->extractTxid($data);
            $gatewayPayload = (array) ($locked->gateway_payload ?? []);
            $gatewayPayload['manual_status_syncs'][] = [
                'synced_at' => now()->toIso8601String(),
                'admin_id' => $admin?->id,
                'response' => $response,
            ];

            $locked->gateway_payload = $gatewayPayload;

            if ($txid && !$locked->txid) {
                $locked->txid = $txid;
            }

            if ($gatewayStatus === 'paid') {
                if (!$locked->credited_ledger_id) {
                    $ledger = $walletService->credit(
                        $locked->user,
                        'deposit',
                        $locked->amount,
                        [
                            'source' => 'oxapay_manual_sync',
                            'gateway_track_id' => $locked->gateway_track_id,
                            'gateway_order_id' => $locked->gateway_order_id,
                            'txid' => $txid,
                            'chain' => $locked->chain,
                            'currency' => $locked->currency,
                            'to_address' => $locked->to_address,
                            'deposit_request_id' => $locked->id,
                        ],
                        $locked,
                        $admin
                    );

                    $locked->credited_ledger_id = $ledger->id;
                    $credited = true;
                }

                $locked->status = 'Completed';
                $locked->reviewed_by = $admin?->id;
                $locked->reviewed_at = now();
            }

            if (in_array($gatewayStatus, ['expired', 'failed'], true) && $locked->status === 'pending') {
                $locked->status = 'expired';
                $locked->reviewed_by = $admin?->id;
                $locked->reviewed_at = now();
                $locked->admin_note = 'Marked ' . $gatewayStatus . ' by OxaPay manual status sync.';
                $expired = true;
            }

            $locked->save();

            ActivityLog::record('deposit.oxapay.status_synced', $admin, $locked, [
                'gateway_status' => $gatewayStatus,
                'gateway_track_id' => $locked->gateway_track_id,
                'credited' => $credited,
            ]);
        });

        if ($credited) {
            $notifications->notifyUser(
                $deposit->user_id,
                'deposit_approved',
                'Deposit completed',
                'Your OxaPay deposit has been confirmed and credited.',
                'success',
                ['deposit_request_id' => $deposit->id, 'gateway_track_id' => $deposit->gateway_track_id],
                true
            );

            $message = 'OxaPay payment is paid. Deposit completed and wallet credited.';
        }

        if ($expired) {
            $notifications->notifyUser(
                $deposit->user_id,
                'deposit_rejected',
                'Deposit expired',
                'Your OxaPay deposit expired before it was confirmed.',
                'warning',
                ['deposit_request_id' => $deposit->id, 'gateway_track_id' => $deposit->gateway_track_id]
            );

            $message = 'OxaPay payment is ' . ($data['status'] ?? 'expired') . '. Deposit marked expired.';
        }

        return back()->with('status', $message);
    }

    public function approve(Request $request, DepositRequest $deposit, WalletService $walletService, NotificationService $notifications): RedirectResponse
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

            if ($locked->gateway === 'oxapay' && !$locked->txid) {
                $error = 'OxaPay payment is not confirmed yet.';
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

        $notifications->notifyUser(
            $deposit->user_id,
            'deposit_approved',
            'Deposit approved',
            'Your deposit has been approved and credited.',
            'success',
            ['deposit_request_id' => $deposit->id],
            true
        );

        return back()->with('status', 'Deposit approved.');
    }

    public function reject(Request $request, DepositRequest $deposit, NotificationService $notifications): RedirectResponse
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

        $notifications->notifyUser(
            $deposit->user_id,
            'deposit_rejected',
            'Deposit rejected',
            $validated['admin_note'],
            'error',
            ['deposit_request_id' => $deposit->id]
        );

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

    private function extractTxid(array $payload): ?string
    {
        $txid = $payload['txID']
            ?? $payload['txid']
            ?? $payload['tx_hash']
            ?? data_get($payload, 'txs.0.tx_hash');

        return $txid ? (string) $txid : null;
    }
}
