<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Illuminate\Support\Str;
use Jenky\Atlas\Body\JsonDecoder;
use Jenky\Atlas\Body\XmlDecoder;
use Jenky\Atlas\Contracts\DecoderAwareInterface;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class SetResponseDecoder
{
    public function __invoke(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request instanceof DecoderAwareInterface) {
            $decoder = function () use ($request, $response) {
                return $request->decode($response);
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
     */
    protected function chooseDecoder(Request $request, Response $response)
    {
        if (Str::contains($response->header('Content-Type'), 'json')) {
            return new JsonDecoder();
        }

        switch ($response->header('Content-Type')) {
            case 'application/xml':
                return new XmlDecoder();

            default:
                return null;
        }
    }
}
