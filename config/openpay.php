<?php

return [
    'merchant_id' => env('OPENPAY_MERCHANT_ID'),
    'public_api_key' => env('OPENPAY_PUBLIC_KEY'),
    'private_api_key' => env('OPENPAY_PRIVATE_KEY'),
    'production' => env('OPENPAY_PRODUCTION_MODE', false),
    'products' => [
        'plan' => 'Wave\\Plan',
    ],
];
