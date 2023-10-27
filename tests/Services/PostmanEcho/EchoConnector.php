<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\PostmanEcho;

use Fansipan\Response;
use Fansipan\Tests\Services\PostmanEcho\Cookie\CookieRequests;
use Fansipan\Traits\ConnectorTrait;

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
