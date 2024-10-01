<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AuthenticatorInterface;

final class BearerAuthenticator implements AuthenticatorInterface
{
    use HeaderAuthenticationTrait;

    public function __construct(string $token, string $tokenPrefix = 'Bearer')
    {
        $this->value = $tokenPrefix.' '.\trim($token);
    }
}
