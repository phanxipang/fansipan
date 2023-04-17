<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Interceptor
{
    /**
     * Add an request interceptor.
     */
    public static function request(Closure $callback): Closure
    {
        return function (RequestInterface $request, callable $next) use ($callback): ResponseInterface {
            return $next($callback($request));
        };
    }

    /**
     * Add a response interceptor.
     */
    public static function response(Closure $callback): Closure
    {
        return function (RequestInterface $request, callable $next) use ($callback): ResponseInterface {
            return $callback($next($request));
        };
    }
}
