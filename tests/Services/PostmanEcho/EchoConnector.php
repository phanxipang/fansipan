<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\PostmanEcho\Cookie\CookieRequests;
use Jenky\Atlas\Traits\ConnectorTrait;

final class EchoConnector implements EchoConnectorInterface
{
    use ConnectorTrait;

    public static function baseUri(): ?string
    {
        return 'https://postman-echo.com/';
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
