<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DepositRequest;
use App\Models\SiteSetting;
use App\Services\NotificationService;
use App\Services\OxaPayService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function create(Request $request, WalletService $walletService, OxaPayService $oxaPay): View
    {
        $setting = SiteSetting::first();
        $minDeposit = (float) ($setting?->min_deposit_usdt ?? 50);
        $reviewHours = (int) ($setting?->deposit_review_hours ?? 24);
        $activeDeposit = null;
        if (Schema::hasColumn('deposit_requests', 'gateway')) {
            $activeDeposit = $request->user()
                ->depositRequests()
                ->where('gateway', 'oxapay')
                ->where('status', 'pending')
                ->whereNotNull('to_address')
                ->orderByDesc('id')
                ->first();
        }

        return view('wallet.deposit', [
            'address' => $activeDeposit?->to_address,
            'qrPayload' => $this->qrPayload($activeDeposit),
            'currency' => $activeDeposit?->pay_currency ?? 'USDT',
            'chain' => $activeDeposit?->chain ?? 'TRC20',
            'minDeposit' => $minDeposit,
            'reviewHours' => $reviewHours,
            'activeDeposit' => $activeDeposit,
            'gatewayReady' => $oxaPay->hasActiveMerchantKey(),
            'walletBalance' => (float) $walletService->getBalance($request->user()),
            'history' => $request->user()
                ->depositRequests()
                ->orderByDesc('id')
                ->limit(20)
                ->get(),
        ]);
    }

    public function store(Request $request, NotificationService $notifications, OxaPayService $oxaPay): RedirectResponse
    {
        $setting = SiteSetting::first();
        $minDeposit = (float) ($setting?->min_deposit_usdt ?? 50);
        $reviewHours = (int) ($setting?->deposit_review_hours ?? 24);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minDeposit],
        ]);

        $orderId = 'PDG-' . now()->format('YmdHis') . '-' . $request->user()->id . '-' . Str::upper(Str::random(6));
        $lifetimeMinutes = max(15, min(2880, $reviewHours * 60));
        $callbackUrl = route('api.oxapay.callback');
        $returnUrl = route('wallet.deposit.success', ['order_id' => $orderId]);

        try {
            $payment = $oxaPay->createWhiteLabelPayment(
                $request->user(),
                $orderId,
                $validated['amount'],
                $callbackUrl,
                $returnUrl,
                'USDT',
                'USDT',
                'TRC20',
                $lifetimeMinutes
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        $data = $payment['data'];
        $trackId = (string) ($data['track_id'] ?? $data['trackId'] ?? '');
        $expiresAt = $this->parseGatewayTimestamp($data['expired_at'] ?? $data['expiredAt'] ?? null)
            ?? now()->addMinutes($lifetimeMinutes);

        $deposit = DepositRequest::create([
            'user_id' => $request->user()->id,
            'currency' => strtoupper((string) ($data['currency'] ?? 'USDT')),
            'chain' => (string) ($data['network'] ?? 'TRC20'),
            'to_address' => (string) $data['address'],
            'amount' => $validated['amount'],
            'txid' => null,
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'gateway' => 'oxapay',
            'gateway_order_id' => $orderId,
            'gateway_track_id' => $trackId ?: null,
            'gateway_payment_url' => $data['payment_url'] ?? $data['paymentUrl'] ?? null,
            'gateway_qr_code' => $data['qr_code'] ?? $data['QRCode'] ?? null,
            'pay_amount' => $data['pay_amount'] ?? $data['payAmount'] ?? null,
            'pay_currency' => strtoupper((string) ($data['pay_currency'] ?? $data['payCurrency'] ?? 'USDT')),
            'gateway_payload' => [
                'request' => $payment['request'],
                'response' => $payment['response'],
                'return_url' => $returnUrl,
                'callback_url' => $callbackUrl,
            ],
        ]);

        $notifications->notifyUser(
            $request->user()->id,
            'deposit_submitted',
            'Deposit address created',
            'Your OxaPay deposit address is ready. Send the exact amount shown on the deposit page.',
            'info',
            ['deposit_request_id' => $deposit->id],
            false,
            $deposit->expires_at
        );

        $notifications->notifyAdminsByRoleOrPermission(
            'deposit.review',
            'deposit_submitted',
            'New OxaPay deposit request',
            'A new OxaPay deposit address was created for a user.',
            'info',
            ['deposit_request_id' => $deposit->id]
        );

        return redirect()->route('wallet.deposit')->with(
            'status',
            'OxaPay deposit address created. Send the exact amount shown before the timer expires.'
        );
    }

    public function success(Request $request): RedirectResponse
    {
        return redirect()->route('wallet.deposit', [
            'order_id' => $request->query('order_id'),
        ])->with('status', 'Payment completed. Your deposit will be credited after OxaPay confirms it.');
    }

    public function webhook(Request $request, OxaPayService $oxaPay, WalletService $walletService, NotificationService $notifications): Response
    {
        $rawPayload = $request->getContent();

        if (!$oxaPay->verifyWebhookSignature($rawPayload, $request->header('HMAC'))) {
            return response('Invalid signature', 400);
        }

        $payload = json_decode($rawPayload, true);

        if (!is_array($payload)) {
            return response('Invalid payload', 400);
        }

        $trackId = (string) ($payload['track_id'] ?? $payload['trackId'] ?? '');
        $orderId = (string) ($payload['order_id'] ?? $payload['orderId'] ?? '');
        $status = Str::lower((string) ($payload['status'] ?? ''));
        $notifiedUserId = null;
        $notificationType = null;

        if ($trackId === '' && $orderId === '') {
            return response('ok');
        }

        DB::transaction(function () use ($trackId, $orderId, $status, $payload, $walletService, &$notifiedUserId, &$notificationType) {
            $query = DepositRequest::query()
                ->where('gateway', 'oxapay')
                ->where(function ($query) use ($trackId, $orderId) {
                    if ($trackId !== '') {
                        $query->orWhere('gateway_track_id', $trackId);
                    }
                    if ($orderId !== '') {
                        $query->orWhere('gateway_order_id', $orderId);
                    }
                });

            $deposit = $query->lockForUpdate()->first();

            if (!$deposit) {
                return;
            }

            $txid = $this->extractTxid($payload);
            $gatewayPayload = (array) ($deposit->gateway_payload ?? []);
            $gatewayPayload['webhook'] = $payload;

            $deposit->gateway_payload = $gatewayPayload;

            if ($txid && !$deposit->txid) {
                $deposit->txid = $txid;
            }

            if ($status === 'paid' && $deposit->status === 'pending' && !$deposit->credited_ledger_id) {
                $ledger = $walletService->credit(
                    $deposit->user,
                    'deposit',
                    $deposit->amount,
                    [
                        'source' => 'oxapay',
                        'gateway_track_id' => $deposit->gateway_track_id,
                        'gateway_order_id' => $deposit->gateway_order_id,
                        'txid' => $txid,
                        'chain' => $deposit->chain,
                        'currency' => $deposit->currency,
                        'to_address' => $deposit->to_address,
                        'deposit_request_id' => $deposit->id,
                    ],
                    $deposit
                );

                $deposit->status = 'approved';
                $deposit->reviewed_at = now();
                $deposit->credited_ledger_id = $ledger->id;
                $notifiedUserId = $deposit->user_id;
                $notificationType = 'approved';
            }

            if (in_array($status, ['expired', 'failed'], true) && $deposit->status === 'pending') {
                $deposit->status = 'expired';
                $deposit->reviewed_at = now();
                $deposit->admin_note = 'Marked ' . $status . ' by OxaPay webhook.';
                $notifiedUserId = $deposit->user_id;
                $notificationType = 'expired';
            }

            $deposit->save();
            ActivityLog::record('deposit.oxapay.webhook', null, $deposit, [
                'status' => $status,
                'track_id' => $trackId,
                'order_id' => $orderId,
            ]);
        });

        if ($notifiedUserId && $notificationType === 'approved') {
            $notifications->notifyUser(
                $notifiedUserId,
                'deposit_approved',
                'Deposit approved',
                'Your OxaPay deposit has been confirmed and credited.',
                'success',
                ['gateway_track_id' => $trackId],
                true
            );
        }

        if ($notifiedUserId && $notificationType === 'expired') {
            $notifications->notifyUser(
                $notifiedUserId,
                'deposit_rejected',
                'Deposit expired',
                'Your OxaPay deposit expired before it was confirmed.',
                'warning',
                ['gateway_track_id' => $trackId]
            );
        }

        return response('ok');
    }

    private function qrPayload(?DepositRequest $deposit): ?string
    {
        if (!$deposit?->to_address) {
            return null;
        }

        $amount = $deposit->pay_amount ?: $deposit->amount;
        $currency = Str::lower((string) ($deposit->pay_currency ?: $deposit->currency));

        return $currency . ':' . $deposit->to_address . '?amount=' . rtrim(rtrim((string) $amount, '0'), '.');
    }

    private function parseGatewayTimestamp(null|int|string $value): mixed
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return \Carbon\Carbon::createFromTimestamp((int) $value);
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
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
