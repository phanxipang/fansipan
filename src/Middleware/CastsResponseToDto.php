<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Contracts\DtoSerializable;
use Jenky\Atlas\Request;

class CastsResponseToDto
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request instanceof DtoSerializable) {
            $response->macro('dto', function () use ($request) {
                /** @var \Jenky\Atlas\Response $this */
                return $this->successful() ? $request->toDto($this) : null;
            });
        }

        return $response;
    }
}
