<?php

declare(strict_types=1);

namespace Fansipan;

final class GenericRequest extends ConnectorlessRequest
{
    private $uri;

    private $method;

    public function __construct(
        string $uri,
        string $method = 'GET'
    ) {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function endpoint(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->method;
    }
}
