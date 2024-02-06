<?php

namespace App\Components\Transport\Consumer;

use App\Components\Transport\Protocol\Amqp\AmqpClientInterface;
use App\Dto\Payment\PaymentCallbackDto;

class AmqpConsumerTransport implements ConsumerTransportnterface
{

    public function __construct(private readonly AmqpClientInterface $amqpClient)
    {
    }

    public function notify(PaymentCallbackDto $callbackDto, string $requester_id): void
    {
        $this->amqpClient
            ->setConnectionCred(config("consumer.customers.$requester_id.options.amqp.connection"))
            ->setExchange(config("consumer.customers.$requester_id.options.amqp.notify.exchange"))
            ->setRoutingKey(config("consumer.customers.$requester_id.options.amqp.notify.routing_key"))
            ->setMessage((string)json_encode($callbackDto))
            ->publish();
    }
}
