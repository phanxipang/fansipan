<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;

final class Interceptor
{
    /**
     * Add an request interceptor.
     */
    public static function request(Closure $callback): Closure
    {
        return function (RequestInterface $request, callable $next) use ($callback) {
            return $next($callback($request));
        };
    }

    /**
     * Add a response interceptor.
     */
    public static function response(Closure $callback): Closure
    {
        return function (RequestInterface $request, callable $next) use ($callback) {
            return $callback($next($request));
        };
    }
}
