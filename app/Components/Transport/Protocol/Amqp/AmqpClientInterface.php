<?php

namespace App\Components\Transport\Protocol\Amqp;

use App\Components\Transport\Protocol\TransportInterface;

interface AmqpClientInterface extends TransportInterface
{
    public function setMessage(string $message): self;

    public function setExchange(string $exchange): self;

    public function setRoutingKey(string $routing_key): self;

    public function setConnectionCred(array $connection_cred): self;

    public function consume(string $consumer_id): void;

    public function callback(mixed $amqpRequest): void;
}
