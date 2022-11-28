<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Contracts\DtoSerializable;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class SetResponseDtoSerializer
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request instanceof DtoSerializable) {
            $response->setDtoSerializer(function (Response $r) use ($request) {
                return $request->toDto($r);
            });
        }

        return $response;
    }
}
