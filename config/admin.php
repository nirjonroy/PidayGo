<?php

return [
    'allowed_ips' => array_filter(array_map('trim', explode(',', env('ADMIN_ALLOWED_IPS', '127.0.0.1,::1')))),
];
