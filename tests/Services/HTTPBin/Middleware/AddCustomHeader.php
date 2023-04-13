<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin\Middleware;

use Psr\Http\Message\RequestInterface;

class AddCustomHeader
{
    public function __invoke(RequestInterface $request, callable $next)
    {
        return $next($request->withHeader('X-From', 'atlas'));
    }
}
