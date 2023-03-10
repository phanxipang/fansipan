<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use GuzzleHttp\Client;
use Jenky\Atlas\Connector;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\PostmanEcho\Cookie\CookieRequests;
use Psr\Http\Client\ClientInterface;

final class EchoConnector extends Connector
{
    protected function defineClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://postman-echo.com/',
            'allow_redirects' => true,
        ]);
    }

    public function get(): Response
    {
        return $this->send(new EchoRequest('get'));
    }

    public function post(): Response
    {
        return $this->send(new EchoRequest('post'));
    }

    public function cookies(): CookieRequests
    {
        return new CookieRequests($this);
    }
}
