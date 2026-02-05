<?php

return [
    'paypal' => [
        'enabled' => true,
        'name' => 'Paypal',
        'route' => 'paypal',
        'currency' => 'USD',
        'image' => 'images/donate/paypal.png',
        //'endpoint' => 'https://api-m.paypal.com', // for production
        'endpoint' => 'https://api-m.sandbox.paypal.com', // for sandbox
        'client_id' => 'PAYPAL_CLIENT_ID',
        'client_secret' => 'PAYPAL_CLIENT_SECRET',
        'mode' => 'sandbox', //live, sandbox
        'package' => [
            [
                'name' => '500 Silk',
                'price' => 5,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100,
                'value' => 10000,
            ],
        ],
    ],
    'stripe' => [
        'enabled' => false,
        'name' => 'Stripe',
        'route' => 'stripe',
        'currency' => 'USD',
        'image' => 'images/donate/stripe.png',
        'endpoint' => 'https://api.stripe.com',
        'secret_key' => 'STRIPE_SECRET_KEY',
        'publishable_key' => 'STRIPE_PUBLISHABLE_KEY',
        'package' => [
            [
                'name' => '500 Silk',
                'price' => 5,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100,
                'value' => 10000,
            ],
        ],
    ],
    'paymentwall' => [
        'enabled' => false,
        'name' => 'Paymentwall',
        'route' => 'paymentwall',
        'currency' => 'USD',
        'image' => 'images/donate/paymentwall.png',
        'public_key' => 'YOUR_PROJECT_KEY',
        'private_key' => 'YOUR_SECRET_KEY',
        'widget_code' => 'p1_1',
        'api_type' => 'vc',
        'authorized_ips' => [
            '174.36.92.186',
            '174.36.96.66',
            '174.36.92.187',
            '174.36.92.192',
            '174.37.14.28',
            '174.36.92.189',
            '174.37.0.50',
            '174.36.92.190',
        ],

        'authorized_ranges' => [
            '216.127.71.0/24',
        ],
        'package' => [

        ],
    ],
    'coinpayments' => [
        'enabled' => false,
        'name' => 'CoinPayments',
        'route' => 'coinpayments',
        'currency' => 'USD',
        'image' => 'images/donate/coinpayments.png',
        'endpoint' => 'https://api.coinpayments.com',
        'merchant_id' => 'COINPAYMENTS_MERCHANT_ID',
        'client_id' => 'COINPAYMENTS_CLIENT_ID',
        'client_secret' => 'COINPAYMENTS_CLIENT_SECRET',
        'package' => [
            [
                'name' => '100 Silk',
                'price' => 1.00,
                'value' => 100,
            ],
            [
                'name' => '500 Silk',
                'price' => 5.00,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10.00,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25.00,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50.00,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75.00,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100.00,
                'value' => 10000,
            ],
            [
                'name' => '25000 Silk',
                'price' => 250.00,
                'value' => 25000,
            ],
        ],
    ],
    'fawaterk' => [
        'enabled' => false,
        'name' => 'Fawaterk',
        'route' => 'fawaterk',
        'currency' => 'EGP',
        'image' => 'images/donate/fawaterk.png',
        //'endpoint' => 'https://app.fawaterk.com', // for production //webhook url: http://localhost/webhook/fawaterk
        'endpoint' => 'https://staging.fawaterk.com', // for sandbox //webhook url: http://localhost/webhook/fawaterk
        'api_key' => 'FAWATERK_API_KEY',
        'provider_key' => 'FAWATERK_PROVIDER_KEY',
        'package' => [
            [
                'name' => '500 Silk',
                'price' => 5.00,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10.00,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25.00,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50.00,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75.00,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100.00,
                'value' => 10000,
            ],
            [
                'name' => '25000 Silk',
                'price' => 250.00,
                'value' => 25000,
            ],
        ],
    ],
    'maxicard' => [
        'enabled' => true,
        'name' => 'MaxiCard',
        'route' => 'maxicard',
        'currency' => 'TL',
        'image' => 'images/donate/maxicard.png',
        'endpoint' => 'https://www.maxigame.org/epin/yukle.php',
        'api_key' => 'MAXICARD_API_KEY',
        'api_password' => 'MAXICARD_API_PASSWORD',
        'package' => [
            [
                'name' => '100 Silk',
                'price' => 1,
                'value' => 100,
            ],
            [
                'name' => '500 Silk',
                'price' => 5,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100,
                'value' => 10000,
            ],
            [
                'name' => '25000 Silk',
                'price' => 250,
                'value' => 25000,
            ],
        ],
    ],
    'hipocard' => [
        'enabled' => false,
        'name' => 'HipoCard',
        'route' => 'hipocard',
        'currency' => 'TL',
        'image' => 'images/donate/hipocard.png',
        //'endpoint' => 'https://www.hipopotamya.com/api/v1/hipocard/epins', // for production
        'endpoint' => 'https://www.hipopotamya.com/api/sandbox/v1/hipocard/epins', // for sandbox
        'api_key' => 'HIPOCARD_API_KEY',
        'api_password' => 'HIPOCARD_API_PASSWORD',
        'package' => [
            [
                'name' => '100 Silk',
                'price' => 1.00,
                'value' => 100,
            ],
            [
                'name' => '500 Silk',
                'price' => 5.00,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10.00,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25.00,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50.00,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75.00,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100.00,
                'value' => 10000,
            ],
            [
                'name' => '25000 Silk',
                'price' => 250.00,
                'value' => 25000,
            ],
        ],
    ],
    'hipopay' => [
        'enabled' => true,
        'name' => 'HipoPay',
        'route' => 'hipopay',
        'currency' => 'TL',
        'image' => 'images/donate/hipopay.png',
        'endpoint' => 'https://www.hipopotamya.com/api/v1/merchants/payment/token', //webhook url: http://localhost/webhook/hipopay
        'api_key' => 'HIPOPAY_API_KEY',
        'api_password' => 'HIPOPAY_API_PASSWORD',
        'commission_type' => 1,
        'package' => [
            [
                'name' => '100 Silk',
                'price' => 1,
                'value' => 100,
            ],
            [
                'name' => '500 Silk',
                'price' => 5,
                'value' => 500,
            ],
            [
                'name' => '1000 Silk',
                'price' => 10,
                'value' => 1000,
            ],
            [
                'name' => '2500 Silk',
                'price' => 25,
                'value' => 2500,
            ],
            [
                'name' => '5000 Silk',
                'price' => 50,
                'value' => 5000,
            ],
            [
                'name' => '7500 Silk',
                'price' => 75,
                'value' => 7500,
            ],
            [
                'name' => '10000 Silk',
                'price' => 100,
                'value' => 10000,
            ],
            [
                'name' => '25000 Silk',
                'price' => 250,
                'value' => 25000,
            ],
        ],
    ],
];
