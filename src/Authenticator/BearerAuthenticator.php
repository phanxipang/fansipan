<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AuthenticatorInterface;

final class BearerAuthenticator implements AuthenticatorInterface
{
    use HeaderAuthorizationTrait;

    public function __construct(string $token, string $tokenPrefix = 'Bearer')
    {
        $this->token = $tokenPrefix.' '.\trim($token);
    }
}
