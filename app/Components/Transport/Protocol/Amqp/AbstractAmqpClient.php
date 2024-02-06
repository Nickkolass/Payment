<?php

namespace App\Components\Transport\Protocol\Amqp;

use App\Http\Controllers\PaymentController;
use App\Http\Request\PaymentRequest;
use Log;
use Throwable;

abstract class AbstractAmqpClient implements AmqpClientInterface
{

    protected string $message;
    protected string $exchange;
    protected string $routing_key;
    protected array $connection_cred;

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function setRoutingKey(string $routing_key): self
    {
        $this->routing_key = $routing_key;
        return $this;
    }

    public function setConnectionCred(array $connection_cred): self
    {
        $this->connection_cred = $connection_cred;
        return $this;
    }

    abstract protected function getBody(mixed $message): array;

    abstract protected function getHeaders(mixed $message): array;

    abstract protected function setConnection(): self;

    abstract protected function basicPublish(): self;

    abstract protected function basicConsume(string $queue, callable $callback): void;

    abstract protected function unsetConnection(): self;

    protected function makeRequest(string $request, mixed $amqpRequest): PaymentRequest
    {
        // если входные данные не проходят валидацию то ошибка выбрасывается на этапе создания реквеста
        // Call to a member function getUrlGenerator() on null
        $request = new $request($this->getBody($amqpRequest));
        $request->headers->add($this->getHeaders($amqpRequest));
        $request->setContainer(app())->validateResolved();
        return $request;
    }

    public function publish(): void
    {
        $this->setConnection()
            ->basicPublish()
            ->unsetConnection();
    }

    public function consume(string $consumer_id): void
    {
        $config = config("consumer.customers.$consumer_id.options.amqp");
        $this->setConnectionCred($config['connection'])->setConnection();
        try {
            $this->basicConsume($config['consume_queue'], [$this, 'callback']);
        } catch (Throwable $exception) {
            $message = $exception->getMessage();
            echo $message . PHP_EOL;
            Log::error($message);
        }
    }

    public function callback(mixed $amqpRequest): void
    {
        $request = $this->makeRequest(PaymentRequest::class, $amqpRequest);
        app(PaymentController::class)->payment($request);
    }
}
