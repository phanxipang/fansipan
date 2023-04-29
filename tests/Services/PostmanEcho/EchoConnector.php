<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use GuzzleHttp\Client;
use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\PostmanEcho\Cookie\CookieRequests;
use Jenky\Atlas\Traits\ConnectorTrait;
use Psr\Http\Client\ClientInterface;

final class EchoConnector implements ConnectorInterface
{
    use ConnectorTrait;

    public function baseUri(): ?string
    {
        return 'https://postman-echo.com/';
    }

    protected function defaultClient(): ClientInterface
    {
        return new Client([
            'base_uri' => $this->baseUri(),
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
