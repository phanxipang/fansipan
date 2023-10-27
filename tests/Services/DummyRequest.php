<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services;

use Fansipan\ConnectorlessRequest;

final class DummyRequest extends ConnectorlessRequest
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $method;

    public function __construct(
        string $endpoint,
        string $method = 'GET'
    ) {
        $this->endpoint = $endpoint;
        $this->method = $method;
    }

    public function endpoint(): string
    {
        return $this->endpoint;
    }

    public function method(): string
    {
        return $this->method;
    }
}
