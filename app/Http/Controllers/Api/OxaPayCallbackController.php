<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\GatewaySetting;
use App\Models\PaymentLog;
use App\Services\NotificationService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OxaPayCallbackController extends Controller
{
    public function __invoke(Request $request, WalletService $walletService, NotificationService $notifications): Response
    {
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);
        $payload = is_array($payload) ? $payload : [];

        $trackId = $this->trackId($payload);
        $orderId = $this->orderId($payload);
        $status = Str::lower((string) ($payload['status'] ?? ''));
        $signature = (string) $request->header('HMAC', '');

        $log = PaymentLog::create([
            'gateway' => 'oxapay',
            'track_id' => $trackId,
            'order_id' => $orderId,
            'status' => $status,
            'signature' => $signature ?: null,
            'headers' => $this->safeHeaders($request),
            'payload' => $payload,
            'raw_body' => $rawBody,
            'message' => 'Received callback.',
        ]);

        $secretKey = $this->secretKey();

        if (!$secretKey) {
            $this->finishLog($log, 400, 'OxaPay secret key is not configured.');
            return response('Secret key not configured', 400);
        }

        if (!$this->signatureIsValid($rawBody, $signature, $secretKey)) {
            $this->finishLog($log, 400, 'Invalid HMAC signature.', false);
            return response('Invalid HMAC signature', 400);
        }

        $log->update([
            'signature_valid' => true,
            'message' => 'Valid signature.',
        ]);

        if ($status !== 'paid') {
            $this->finishLog($log, 200, 'Callback logged; status is not paid.', true);
            return response('ok');
        }

        if ($trackId === '') {
            $this->finishLog($log, 422, 'Missing OxaPay track id.', true);
            return response('Missing track id', 422);
        }

        $deposit = DepositRequest::query()
            ->where('gateway', 'oxapay')
            ->where('gateway_track_id', $trackId)
            ->first();

        if (!$deposit) {
            $this->finishLog($log, 404, 'Deposit not found for track id.', true);
            return response('Deposit not found', 404);
        }

        $log->update(['deposit_request_id' => $deposit->id]);

        $credited = false;

        DB::transaction(function () use ($deposit, $payload, $trackId, $orderId, $walletService, &$credited) {
            $locked = DepositRequest::query()
                ->whereKey($deposit->id)
                ->lockForUpdate()
                ->first();

            if (!$locked || $locked->credited_ledger_id || $locked->status === 'Completed') {
                return;
            }

            $txid = $this->txid($payload);
            $gatewayPayload = (array) ($locked->gateway_payload ?? []);
            $gatewayPayload['callback'] = $payload;

            $ledger = $walletService->credit(
                $locked->user,
                'deposit',
                $locked->amount,
                [
                    'source' => 'oxapay',
                    'gateway_track_id' => $trackId,
                    'gateway_order_id' => $orderId ?: $locked->gateway_order_id,
                    'txid' => $txid,
                    'chain' => $locked->chain,
                    'currency' => $locked->currency,
                    'to_address' => $locked->to_address,
                    'deposit_request_id' => $locked->id,
                ],
                $locked
            );

            $locked->update([
                'txid' => $locked->txid ?: $txid,
                'status' => 'Completed',
                'reviewed_at' => now(),
                'credited_ledger_id' => $ledger->id,
                'gateway_payload' => $gatewayPayload,
            ]);

            $credited = true;
        });

        if ($credited) {
            $notifications->notifyUser(
                $deposit->user_id,
                'deposit_approved',
                'Deposit completed',
                'Your OxaPay deposit has been confirmed and credited.',
                'success',
                ['deposit_request_id' => $deposit->id, 'gateway_track_id' => $trackId],
                true
            );
        }

        $this->finishLog($log, 200, $credited ? 'Deposit completed and wallet credited.' : 'Deposit already processed.', true);

        return response('ok');
    }

    private function secretKey(): ?string
    {
        if (!Schema::hasTable('gateway_settings')) {
            return null;
        }

        return GatewaySetting::query()
            ->where('gateway_name', 'oxapay')
            ->where('is_active', true)
            ->first()
            ?->secret_key;
    }

    private function signatureIsValid(string $rawBody, string $signature, string $secretKey): bool
    {
        if ($rawBody === '' || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $rawBody, $secretKey), $signature);
    }

    private function trackId(array $payload): string
    {
        return (string) ($payload['trackId'] ?? $payload['track_id'] ?? '');
    }

    private function orderId(array $payload): string
    {
        return (string) ($payload['orderId'] ?? $payload['order_id'] ?? '');
    }

    private function txid(array $payload): ?string
    {
        $txid = $payload['txID']
            ?? $payload['txid']
            ?? $payload['tx_hash']
            ?? data_get($payload, 'txs.0.tx_hash');

        return $txid ? (string) $txid : null;
    }

    private function safeHeaders(Request $request): array
    {
        return collect($request->headers->all())
            ->except(['authorization', 'cookie', 'x-xsrf-token'])
            ->all();
    }

    private function finishLog(PaymentLog $log, int $responseCode, string $message, bool $signatureValid = false): void
    {
        $log->update([
            'response_code' => $responseCode,
            'message' => $message,
            'signature_valid' => $signatureValid,
        ]);
    }
}
