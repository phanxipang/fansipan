<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\PostmanEcho;

use Fansipan\ConnectorlessRequest;

final class CurrentUtcRequest extends ConnectorlessRequest
{
    public function endpoint(): string
    {
        return 'https://postman-echo.com/time/now';
    }
}
