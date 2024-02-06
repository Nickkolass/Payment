<?php

namespace App\Components\Transport\Protocol\Http;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpLaravelClient extends AbstractHttpClient
{
    public function publish(): void
    {
        $this->send();
    }

    public function send(): PromiseInterface|Response
    {
        return Http::withQueryParameters($this->query)
            ->withHeaders($this->headers)
            ->when($this->async, function (PendingRequest $request) {
                $request->async();
            })
            ->send($this->method, $this->url);
    }

    public function sendAsync(): PromiseInterface|Response
    {
        $this->async = true;
        return $this->send();
    }
}
