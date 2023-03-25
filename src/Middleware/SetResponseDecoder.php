<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

final class SetResponseDecoder
{
    public function __invoke(Request $request, callable $next): Response
    {
        $response = $next($request);

        $response->setDecoder($request->decoder());

        return $response;
    }
}
