<?php

return [
    'package' => [
        'module' => 'CoinPayments',
        'version' => '1.0',
    ],
    'routes' => [
        'static' => [
            'api/coinpayments/order' => \Modules\CoinPayments\API\Order::class,
            'api/coinpayments/notifications' => \Modules\CoinPayments\API\Notifications::class,
        ]
    ],
    'js' => [
        // Module Name
        'CoinPayments' => [
            // Source file => Dest file
            'CoinPayments.js' => 'Checkout.min.js',
        ]
    ],
    'modules' => [
        'coinpayments' => [
            'public' => null,
            'private' => null,
            'merchantId' => null,
            'ipnSecret' => null,
        ],
    ]
];
