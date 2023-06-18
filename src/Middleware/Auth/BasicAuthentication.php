<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware\Auth;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class BasicAuthentication
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $credential = $this->username.':'.$this->password;

        return $next($request->withHeader('Authorization', base64_encode($credential)));
    }
}
