<?php

namespace App\Components\Transport\Protocol\Http;

abstract class AbstractHttpClient implements HttpClientInterface
{

    protected array $query;
    protected string $url;
    protected string $method;
    protected bool $async = false;
    protected array $headers = [];

    public function setQuery(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers += [$name => $value];
        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }
}
