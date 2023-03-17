<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho\Cookie;

use Jenky\Atlas\Request;

class GetCookiesRequest extends Request
{
    public function endpoint(): string
    {
        return '/cookies';
    }
}
