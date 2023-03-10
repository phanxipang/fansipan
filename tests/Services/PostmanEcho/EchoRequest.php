<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use Jenky\Atlas\ConnectorlessRequest;

class EchoRequest extends ConnectorlessRequest
{
    public function endpoint(): string
    {
        return 'postman-echo.com/get';
    }
}
