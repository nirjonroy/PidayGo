<?php

namespace App\Providers;

use App\Models\GatewaySetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class GatewaySettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        config([
            'oxapay.gateway_name' => 'oxapay',
            'oxapay.api_key' => null,
            'oxapay.secret_key' => null,
            'oxapay.is_active' => false,
        ]);

        if (!Schema::hasTable('gateway_settings')) {
            return;
        }

        $settings = GatewaySetting::query()
            ->where('gateway_name', 'oxapay')
            ->first();

        if (!$settings || !$settings->is_active) {
            return;
        }

        config([
            'oxapay.gateway_name' => $settings->gateway_name,
            'oxapay.api_key' => $settings->api_key,
            'oxapay.secret_key' => $settings->secret_key,
            'oxapay.is_active' => true,
        ]);
    }
}
