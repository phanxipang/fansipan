<?php

declare(strict_types=1);

namespace Fansipan;

final class GenericRequest extends ConnectorlessRequest
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
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
