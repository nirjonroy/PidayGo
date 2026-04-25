<?php

return [
    'gateway_name' => 'oxapay',
    'api_key' => null,
    'secret_key' => null,
    'is_active' => false,
    'white_label_url' => env('OXAPAY_WHITE_LABEL_URL', 'https://api.oxapay.com/v1/payment/white-label'),
];
