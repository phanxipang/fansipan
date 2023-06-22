<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware\Auth;

final class BearerAuthentication
{
    use AuthenticationMiddlewareTrait;

    public function __construct(string $token, string $tokenPrefix = 'Bearer')
    {
        $this->token = $tokenPrefix.' '.trim($token);
    }
}
