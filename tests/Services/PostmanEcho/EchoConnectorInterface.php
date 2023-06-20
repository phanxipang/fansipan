<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\PostmanEcho\Cookie\CookieRequests;

interface EchoConnectorInterface extends ConnectorInterface
{
    public function get(): Response;

    public function post(): Response;

    public function cookies(): CookieRequests;
}
