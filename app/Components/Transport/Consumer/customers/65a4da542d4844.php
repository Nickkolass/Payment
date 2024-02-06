<?php

return [

    'reply_to_driver' => 'amqp',
    'options' => [
        'http' => [
            'notify_url' => 'host.docker.internal:8876/api/payment/callback',
        ],
        'amqp' => [
            'consume_queue' => 'payment',
            'notify' => [
                'exchange' => 'shop',
                'routing_key' => 'shop',
            ],
            'connection' => [
                'host' => 'host.docker.internal',
                'port' => 5672,
                'user' => 'root',
                'password' => 'root',
            ],
        ],
    ],
];


