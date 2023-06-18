<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware\Auth;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class TokenAuthentication
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $token, string $tokenPrefix = 'Bearer ')
    {
        $this->token = $tokenPrefix.trim($token);
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        return $next($request->withHeader('Authorization', $this->token));
    }
}
