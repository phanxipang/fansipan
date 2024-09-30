<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AuthenticatorInterface;

final class HeaderAuthenticator implements AuthenticatorInterface
{
    use HeaderAuthorizationTrait;

    /**
     * @param  string|\Stringable $value
     */
    public function __construct(string $header, $value)
    {
        $this->header = $header;
        $this->value = $value;
    }
}
