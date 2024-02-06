<?php

use App\Components\Transport\Protocol\Amqp\RabbitmqClient;
use App\Components\Transport\Protocol\Http\HttpLaravelClient;

return [

    'protocols' => [
        'amqp' => [
            'default' => 'rabbitmq',
            'clients' => [
                'rabbitmq' => [
                    'bind' => RabbitmqClient::class,
                ],
            ],
        ],

        'http' => [
            'default' => 'laravel',
            'clients' => [
                'laravel' => [
                    'bind' => HttpLaravelClient::class,
                ],
            ],
        ],
    ],
];
