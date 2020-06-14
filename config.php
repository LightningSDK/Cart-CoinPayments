<?php

return [
    'package' => [
        'module' => 'CoinPayments',
        'version' => '1.0',
    ],
    'routes' => [
        'static' => [
            'api/coinpayments/order' => \lightningsdk\checkout_coinpayments\API\Order::class,
            'api/coinpayments/notifications' => \lightningsdk\checkout_coinpayments\API\Notifications::class,
        ]
    ],
    'js' => [
        // Module Name
        'lightningsdk/coinpayments' => [
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
