<?php

use App\Components\Transport\Consumer\AmqpConsumerTransport;
use App\Components\Transport\Consumer\HttpConsumerTransport;

return [
    'cache_prefix' => 'payment_reply_to_',
    'drivers' => [
        'amqp' => AmqpConsumerTransport::class,
        'http' => HttpConsumerTransport::class,
    ],
];


