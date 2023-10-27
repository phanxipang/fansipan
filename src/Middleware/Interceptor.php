<?php

declare(strict_types=1);

namespace Fansipan\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Interceptor
{
    /**
     * Add a request interceptor.
     */
    public static function request(Closure $callback): Closure
    {
        return static function (RequestInterface $request, callable $next) use ($callback): ResponseInterface {
            return $next($callback($request));
        };
    }

    /**
     * Add a response interceptor.
     */
    public static function response(Closure $callback): Closure
    {
        return static function (RequestInterface $request, callable $next) use ($callback): ResponseInterface {
            return $callback($next($request));
        };
    }
}
