<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware\Auth;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait AuthenticationMiddlewareTrait
{
    /**
     * @var string|\Stringable
     */
    private $token;

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        return $next($request->withHeader('Authorization', (string) $this->token));
    }
}
