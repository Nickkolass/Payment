<?php

namespace App\Components\Transport\Consumer;


use App\Components\Transport\Protocol\Http\HttpClientInterface;
use App\Dto\Payment\PaymentCallbackDto;

class HttpConsumerTransport implements ConsumerTransportnterface
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function notify(PaymentCallbackDto $callbackDto, string $requester_id): void
    {
        $this->httpClient
            ->setQuery((array)$callbackDto)
            ->setMethod('POST')
            ->setUrl(config("consumer.customers.$requester_id.options.http.notify_url"))
            ->publish();
    }
}
