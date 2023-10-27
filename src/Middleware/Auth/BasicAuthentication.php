<?php

declare(strict_types=1);

namespace Fansipan\Middleware\Auth;

final class BasicAuthentication
{
    use AuthenticationMiddlewareTrait;

    public function __construct(string $username, string $password)
    {
        $this->token = 'Basic '.\base64_encode($username.':'.$password);
    }
}
