<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;

class Connector implements ConnectorInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;
    use Traits\HasRequestCollection;

    /**
     * Assign connector to given request.
     */
    public function request(Request $request): Request
    {
        return $request->withConnector($this);
    }

    /**
     * Send the request.
     */
    public function send(Request $request): Response
    {
        return PendingRequest::from($this->request($request))->send();
    }
}
