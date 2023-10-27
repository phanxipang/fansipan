<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\PostmanEcho;

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Response;
use Fansipan\Tests\Services\PostmanEcho\Cookie\CookieRequests;

interface EchoConnectorInterface extends ConnectorInterface
{
    public function get(): Response;

    public function post(): Response;

    public function cookies(): CookieRequests;
}
