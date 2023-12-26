<?php

return [

    'default' => env('PAYMENT_CONNECTION', 'yookassa'),

    'connections' => [
        'stub' => [
            'bind' => \App\Components\Payment\StubClient::class,
        ],

        'yookassa' => [
            'bind' => \App\Components\Payment\YooKassaClient::class,
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
