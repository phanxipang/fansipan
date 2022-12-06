<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;

class Connector implements ConnectorInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;

    /**
     * Send the request.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return \Jenky\Atlas\Response
     */
    public function send(Request $request): Response
    {
        return PendingRequest::from(
            $request->withConnector($this)
        )->send();
    }
}
