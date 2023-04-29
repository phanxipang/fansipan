<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\PendingRequest;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

trait ConnectorTrait
{
    use HasClient;
    use HasMiddleware;

    /**
     * Get the base uri for the HTTP client.
     */
    public function baseUri(): ?string
    {
        return null;
    }

    /**
     * Send the given request.
     *
     * The request and response should be processed through middleware.
     */
    public function send(Request $request): Response
    {
        return (new PendingRequest($this))->send($request);
    }
}
