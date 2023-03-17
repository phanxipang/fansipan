<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho\Cookie;

use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\PostmanEcho\EchoConnector;

class CookieRequests
{
    /**
     * @var \Jenky\Atlas\Tests\Services\PostmanEcho\EchoConnector
     */
    private $connector;

    public function __construct(EchoConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param  array<string, mixed> $cookies
     */
    public function set(array $cookies): Response
    {
        return $this->connector->send(new SetCookiesRequest($cookies));
    }

    public function get(): Response
    {
        return $this->connector->send(new GetCookiesRequest());
    }
}
