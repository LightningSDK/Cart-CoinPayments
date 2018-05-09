<?php

return [
    'package' => [
        'module' => 'CoinPayments',
        'version' => '1.0',
    ],
    'routes' => [
        'static' => [
            'api/coinpayments/order' => 'Modules\\CoinPayments\\API\\Order',
            'api/coinpayments/notifications' => 'Modules\\CoinPayments\\API\\Notifications',
        ]
    ],
    'js' => [
        // Module Name
        'CoinPayments' => [
            // Source file => Dest file
            'CoinPayments.js' => 'Checkout.min.js',
        ]
    ]
];
