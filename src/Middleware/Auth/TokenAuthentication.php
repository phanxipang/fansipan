<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware\Auth;

final class TokenAuthentication
{
    use AuthenticationMiddlewareTrait;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public static function from(string $token, string $tokenPrefix = 'Bearer'): self
    {
        return new self($tokenPrefix.' '.trim($token));
    }
}
