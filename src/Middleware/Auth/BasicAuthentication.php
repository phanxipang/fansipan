<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware\Auth;

final class BasicAuthentication
{
    use AuthenticationMiddlewareTrait;

    public function __construct(#[\SensitiveParameter] string $username, #[\SensitiveParameter] string $password)
    {
        $credential = $username.':'.$password;

        $this->token = 'Basic '.base64_encode($credential);
    }
}
