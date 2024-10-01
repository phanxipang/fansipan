<?php

declare(strict_types=1);

namespace Fansipan\Middleware\Auth;

use Fansipan\Authenticator\BearerAuthenticator;
use Fansipan\Middleware\Authentication;

@trigger_error(sprintf('The "%s" class is deprecated since v1.2, use "%s" middleware and "%s" instead.', BearerAuthentication::class, Authentication::class, BearerAuthenticator::class), \E_USER_DEPRECATED);

/**
 * @deprecated since v1.2 Use Authentication middleware with BearerAuthenticator instead.
 * @codeCoverageIgnore
 */
final class BearerAuthentication
{
    use AuthenticationMiddlewareTrait;

    public function __construct(string $token, string $tokenPrefix = 'Bearer')
    {
        $this->token = $tokenPrefix.' '.\trim($token);
    }
}
