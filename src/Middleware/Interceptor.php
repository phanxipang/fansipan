<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class Interceptor
{
    /**
     * Add an request interceptor.
     */
    public static function request(Closure $callback): Closure
    {
        return function (Request $request, Closure $next) use ($callback): Response {
            $callback($request);

            return $next($request);
        };
    }

    /**
     * Add a response interceptor.
     */
    public static function response(Closure $callback): Closure
    {
        return function (Request $request, Closure $next) use ($callback): Response {
            $callback($response = $next($request));

            return $response;
        };
    }
}
