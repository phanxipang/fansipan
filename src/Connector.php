<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;

abstract class Connector implements ConnectorInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;

    /**
     * Create a new pending request.
     */
    protected function request(Request $request): PendingRequest
    {
        return new PendingRequest($this, $request);
    }

    public function send(Request $request): Response
    {
        return $this->request($request)->send();
    }
}
