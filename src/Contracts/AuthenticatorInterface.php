<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

use Psr\Http\Message\RequestInterface;

interface AuthenticatorInterface
{
    public function authenticate(RequestInterface $request): RequestInterface;
}
