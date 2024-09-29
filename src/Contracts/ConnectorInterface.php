<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

use Fansipan\Middleware;
use Fansipan\Request;
use Fansipan\Response;
use Psr\Http\Client\ClientInterface;

interface ConnectorInterface
{
    /**
     * Get the HTTP client instance.
     */
    public function client(): ClientInterface;

    /**
     * Get the middleware instance.
     */
    public function middleware(): Middleware;

    /**
     * Send the given request.
     *
     * The request and response should be processed through middleware.
     *
     * @template T of object
     *
     * @param  Request<T> $request
     * @return Response<T>
     */
    public function send(Request $request): Response;
}
