<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\PostmanEcho\Cookie;

use Fansipan\Request;

class GetCookiesRequest extends Request
{
    public function endpoint(): string
    {
        return '/cookies';
    }
}
