<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AuthenticatorInterface;

final class BasicAuthenticator implements AuthenticatorInterface
{
    use HeaderAuthorizationTrait;

    public function __construct(string $username, string $password)
    {
        $this->value = 'Basic '.\base64_encode($username.':'.$password);
    }
}
