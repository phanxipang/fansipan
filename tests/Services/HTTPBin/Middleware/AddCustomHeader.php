<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin\Middleware;

use Closure;
use Jenky\Atlas\Request;

class AddCustomHeader
{
    public function __invoke(Request $request, Closure $next)
    {
        $request->headers()->with('X-From', 'atlas');

        return $next($request);
    }
}
