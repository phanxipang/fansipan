<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Body\JsonDecoder;
use Jenky\Atlas\Contracts\ResponseDecodable;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class SetResponseDecoder
{
    public function __invoke(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request instanceof ResponseDecodable) {
            $decoder = function () use ($request, $response) {
                return $request->decodeResponse($response);
            };
        } else {
            $decoder = $this->chooseDecoder($request, $response);
        }

        if ($decoder) {
            $response->decoder($decoder);
        }

        return $response;
    }

    /**
     * Choose the appropriate decoder.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @param  \Jenky\Atlas\Response  $response
     * @return null|callable
     */
    protected function chooseDecoder(Request $request, Response $response)
    {
        switch ($response->header('Content-Type')) {
            case 'application/json':
                return new JsonDecoder();

            default:
                return null;
        }
    }
}
