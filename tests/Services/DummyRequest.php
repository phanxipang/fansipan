<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services;

use Jenky\Atlas\ConnectorlessRequest;

final class DummyRequest extends ConnectorlessRequest
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    protected $method;

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
}
