<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

final class AttachContentTypeRequestHeader
{
    public function __invoke(Request $request, callable $next): Response
    {
        if ($request->headers()->has('Content-Type')) {
            return $next($request);
        }

        if ($contentType = $request->body()->contentType()) {
            $request->headers()->with('Content-Type', $contentType);
        }

        return $next($request);
    }
}