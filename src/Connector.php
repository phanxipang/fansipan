<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryableInterface;

abstract class Connector implements ConnectorInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;

    public function baseUri(): ?string
    {
        return null;
    }

    public function send(Request $request): Response
    {
        $pendingRequest = new PendingRequest($this);

        return $this instanceof RetryableInterface
            ? $pendingRequest->sendAndRetry($request)
            : $pendingRequest->send($request);
    }
}
