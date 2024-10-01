<?php

declare(strict_types=1);

namespace Fansipan\Middleware\Auth;

use Fansipan\Authenticator\BasicAuthenticator;
use Fansipan\Middleware\Authentication;

@trigger_error(sprintf('The "%s" class is deprecated since v1.2, use "%s" middleware and "%s" instead.', BearerAuthentication::class, Authentication::class, BasicAuthenticator::class), \E_USER_DEPRECATED);

/**
 * @deprecated since v1.2 Use Authentication middleware with BasicAuthenticator instead.
 * @codeCoverageIgnore
 */
final class BasicAuthentication
{
    use AuthenticationMiddlewareTrait;

    public function __construct(string $username, string $password)
    {
        $this->token = 'Basic '.\base64_encode($username.':'.$password);
    }
}
