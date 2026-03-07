<?php

return [
    // Default to "no restriction" unless explicitly enabled in .env
    // Example:
    // ADMIN_IP_RESTRICTION=true
    // ADMIN_ALLOWED_IPS=203.0.113.10,203.0.113.11
    'allowed_ips' => array_filter(array_map('trim', explode(',', env('ADMIN_ALLOWED_IPS', '')))),
    'enforce_ip' => env('ADMIN_IP_RESTRICTION', false),
];
