<?php

namespace App\Components\Transport\Protocol\Amqp;


use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqClient extends AbstractAmqpClient
{

    private AMQPStreamConnection $connection;
    private AbstractChannel|AMQPChannel $channel;

    protected function setConnection(): self
    {
        $this->connection = new AMQPStreamConnection(...$this->connection_cred);
        $this->channel = $this->connection->channel();
        return $this;
    }

    protected function unsetConnection(): self
    {
        $this->channel->close();
        $this->connection->close();
        unset($this->channel, $this->connection);
        return $this;
    }

    protected function basicPublish(): self
    {
        $this->channel->basic_publish(
            new AMQPMessage($this->message),
            $this->exchange,
            $this->routing_key,
        );
        return $this;
    }

    protected function basicConsume(string $queue, callable $callback): void
    {
        $this->channel->basic_consume($queue, no_ack: true, callback: $callback);
        $this->channel->consume();
    }

    protected function getBody(mixed $message): array
    {
        /** @var AMQPMessage $message */
        return json_decode($message->getBody(), true);
    }

    protected function getHeaders(mixed $message): array
    {
        /** @var AMQPMessage $message */
        return ['requester-id' => $message->get('reply_to')];
    }
}
