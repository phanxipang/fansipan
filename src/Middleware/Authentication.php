<?php

declare(strict_types=1);

namespace Fansipan\Middleware;

use Fansipan\Contracts\AuthenticatorInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Authentication
{
    /**
     * @var AuthenticatorInterface
     */
    private $authenticator;

    public function __construct(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        return $next($this->authenticator->authenticate($request));
    }
}
