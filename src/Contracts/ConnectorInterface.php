<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Middleware;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
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
    // public function middleware(): Middleware;

    /**
     * Send the given request.
     *
     * The request and response should be processed through middleware.
     */
    public function send(Request $request): Response;
}
