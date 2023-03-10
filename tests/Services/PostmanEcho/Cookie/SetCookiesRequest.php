<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho\Cookie;

use Jenky\Atlas\Request;

class SetCookiesRequest extends Request
{
    /**
     * @var array
     */
    private $cookies;

    /**
     * @param  array<string, mixed> $cookies
     */
    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function endpoint(): string
    {
        return '/cookies/set';
    }
}
