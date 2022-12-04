<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Contracts\DtoSerializable;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class CastsResponseToDto
{
    public function __invoke(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request instanceof DtoSerializable) {
            $response->macro('dto', function () use ($request) {
                /* @var \Jenky\Atlas\Response $this */
                return $this->successful() ? $request->toDto($this) : null;
            });
        }

        return $response;
    }
}
