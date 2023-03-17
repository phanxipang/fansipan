<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use Jenky\Atlas\ConnectorlessRequest;

final class CurrentUtcRequest extends ConnectorlessRequest
{
    public function endpoint(): string
    {
        return 'https://postman-echo.com/time/now';
    }
}
