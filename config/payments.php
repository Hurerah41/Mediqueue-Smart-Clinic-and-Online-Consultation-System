<?php

return [
    'currency' => env('PAYMENT_CURRENCY', 'PKR'),
    'gateway' => env('PAYMENT_GATEWAY', 'local_test'),

    'online_appointments' => [
        'enabled' => env('ONLINE_APPOINTMENT_PAYMENTS_ENABLED', true),
    ],

    'gateways' => [
        'local_test' => [
            'label' => 'Local Test Checkout',
        ],
        'jazzcash' => [
            'label' => 'JazzCash',
            'merchant_id' => env('JAZZCASH_MERCHANT_ID'),
            'password' => env('JAZZCASH_PASSWORD'),
            'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT'),
            'sandbox' => env('JAZZCASH_SANDBOX', true),
        ],
        'payfast' => [
            'label' => 'PayFast',
            'merchant_id' => env('PAYFAST_MERCHANT_ID'),
            'secured_key' => env('PAYFAST_SECURED_KEY'),
            'sandbox' => env('PAYFAST_SANDBOX', true),
        ],
        'safepay' => [
            'label' => 'Safepay',
            'public_key' => env('SAFEPAY_PUBLIC_KEY'),
            'secret_key' => env('SAFEPAY_SECRET_KEY'),
            'sandbox' => env('SAFEPAY_SANDBOX', true),
        ],
    ],
];
