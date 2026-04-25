<?php

namespace App\Services;

use App\Models\GatewaySetting;
use App\Models\DepositRequest;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class OxaPayService
{
    private const GATEWAY_NAME = 'oxapay';

    public function hasActiveMerchantKey(): bool
    {
        return filled($this->merchantApiKey());
    }

    public function connectionStatus(): array
    {
        $apiKey = $this->merchantApiKey();

        if (!$apiKey) {
            return [
                'connected' => false,
                'message' => 'OxaPay API key is not configured.',
            ];
        }

        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->withHeaders([
                    'merchant_api_key' => $apiKey,
                ])
                ->get(config('oxapay.accepted_currencies_url'));

            $json = $response->json();
            $connected = $response->ok() && is_array($json) && (int) ($json['status'] ?? 0) === 200;

            return [
                'connected' => $connected,
                'message' => $connected
                    ? 'Connected'
                    : (string) data_get($json, 'error.message', $json['message'] ?? 'OxaPay API check failed.'),
                'response' => is_array($json) ? $json : null,
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'message' => 'OxaPay API is unreachable.',
            ];
        }
    }

    public function paymentStatus(DepositRequest $deposit): array
    {
        $apiKey = $this->merchantApiKey();

        if (!$apiKey) {
            throw new RuntimeException('OxaPay merchant API key is not configured.');
        }

        if (empty($deposit->gateway_track_id)) {
            throw new RuntimeException('This deposit does not have an OxaPay track ID.');
        }

        $url = (string) config('oxapay.payment_status_url');
        $query = [];

        if (str_contains($url, '{track_id}')) {
            $url = str_replace('{track_id}', rawurlencode($deposit->gateway_track_id), $url);
        } else {
            $query = [
                'track_id' => $deposit->gateway_track_id,
                'trackId' => $deposit->gateway_track_id,
            ];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'merchant_api_key' => $apiKey,
            ])
            ->get($url, $query);

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('OxaPay returned an invalid status response.');
        }

        $json['http_status'] = $response->status();

        if (!$response->successful() || (int) ($json['status'] ?? 0) !== 200) {
            throw new RuntimeException($this->errorMessage($json));
        }

        return $json;
    }

    public function createWhiteLabelPayment(
        User $user,
        string $orderId,
        float|string $amount,
        string $callbackUrl,
        string $returnUrl,
        string $currency = 'USDT',
        string $payCurrency = 'USDT',
        string $network = 'TRC20',
        int $lifetime = 60
    ): array {
        $apiKey = $this->merchantApiKey();

        if (!$apiKey) {
            throw new RuntimeException('OxaPay merchant API key is not configured.');
        }

        $payload = [
            'amount' => (float) $amount,
            'currency' => strtoupper($currency),
            'pay_currency' => strtoupper($payCurrency),
            'network' => strtoupper($network),
            'lifetime' => max(15, min(2880, $lifetime)),
            'to_currency' => 'USDT',
            'auto_withdrawal' => false,
            'callback_url' => $callbackUrl,
            'return_url' => $returnUrl,
            'email' => $user->email,
            'order_id' => $orderId,
            'description' => 'PidayGo deposit ' . $orderId,
        ];

        $result = $this->post(config('oxapay.white_label_url'), $apiKey, $payload);

        if (!$this->isSuccessful($result) && $this->looksLikeUnsupportedReturnUrl($result)) {
            $fallbackPayload = $payload;
            unset($fallbackPayload['return_url']);
            $result = $this->post(config('oxapay.white_label_url'), $apiKey, $fallbackPayload);
            $payload = $fallbackPayload;
        }

        if (!$this->isSuccessful($result)) {
            throw new RuntimeException($this->errorMessage($result));
        }

        $data = $result['data'] ?? [];

        if (empty($data['address'])) {
            throw new RuntimeException('OxaPay did not return a payment address.');
        }

        return [
            'data' => $data,
            'request' => $payload,
            'response' => $result,
        ];
    }

    public function verifyWebhookSignature(string $rawPayload, ?string $hmac): bool
    {
        $secretKey = $this->webhookSecretKey();

        if (!$secretKey || !$hmac) {
            return false;
        }

        return hash_equals(
            hash_hmac('sha512', $rawPayload, $secretKey),
            $hmac
        );
    }

    private function merchantApiKey(): ?string
    {
        if (!Schema::hasTable('gateway_settings')) {
            return null;
        }

        $settings = GatewaySetting::query()
            ->where('gateway_name', self::GATEWAY_NAME)
            ->where('is_active', true)
            ->first();

        return $settings?->api_key;
    }

    private function webhookSecretKey(): ?string
    {
        if (!Schema::hasTable('gateway_settings')) {
            return null;
        }

        $settings = GatewaySetting::query()
            ->where('gateway_name', self::GATEWAY_NAME)
            ->where('is_active', true)
            ->first();

        return $settings?->secret_key;
    }

    private function post(string $url, string $apiKey, array $payload): array
    {
        $response = Http::timeout(25)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'merchant_api_key' => $apiKey,
            ])
            ->post($url, $payload);

        $json = $response->json();

        if (!is_array($json)) {
            return [
                'status' => $response->status(),
                'message' => 'OxaPay returned an invalid response.',
                'error' => [],
            ];
        }

        $json['http_status'] = $response->status();

        return $json;
    }

    private function isSuccessful(array $result): bool
    {
        return (int) ($result['status'] ?? 0) === 200
            && empty(array_filter((array) ($result['error'] ?? [])))
            && !empty($result['data']);
    }

    private function looksLikeUnsupportedReturnUrl(array $result): bool
    {
        $message = Str::lower($this->errorMessage($result));

        return str_contains($message, 'return_url') || str_contains($message, 'return url');
    }

    private function errorMessage(array $result): string
    {
        $error = $result['error'] ?? null;

        if (is_array($error)) {
            $error = collect($error)->flatten()->filter()->first();
        }

        return (string) ($error ?: ($result['message'] ?? 'Unable to create OxaPay payment.'));
    }
}
