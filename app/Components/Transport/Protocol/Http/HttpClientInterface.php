<?php

namespace App\Components\Transport\Protocol\Http;

use App\Components\Transport\Protocol\TransportInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

interface HttpClientInterface extends TransportInterface
{
    public function send(): PromiseInterface|Response;

    public function sendAsync(): PromiseInterface|Response;

    public function setHeader(string $name, string $value): self;

    public function setQuery(array $query): self;

    public function setUrl(string $url): self;

    public function setMethod(string $method): self;
}
