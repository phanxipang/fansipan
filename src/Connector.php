<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;

abstract class Connector implements ConnectorInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;

    public function send(Request $request): Response
    {
        return (new PendingRequest($this, $request))->send();
    }
}
