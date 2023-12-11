<?php

use App\Components\Payment\YooKassaClient;

return [

    'default' => env('PAYMENT_CONNECTION', 'stub'),

    'connections' => [
        'stub' => [
            'bind' => \App\Components\Payment\StubClient::class,
            'shop' => [
                'login' => 'stub',
                'password' => 'stub'
            ],
            'agent' => [
                'login' => 'stub',
                'password' => 'stub'
            ],
        ],

        'yookassa' => [
            'bind' => YooKassaClient::class,
            'shop' => [
                'login' => env('YOO_KASSA_SHOP_ID'),
                'password' => env('YOO_KASSA_SHOP_TOKEN')
            ],
            'agent' => [
                'login' => env('YOO_KASSA_AGENT_ID'),
                'password' => env('YOO_KASSA_AGENT_TOKEN')
            ],
        ],
    ],
];
