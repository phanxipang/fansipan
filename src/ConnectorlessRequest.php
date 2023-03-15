<?php

declare(strict_types=1);

namespace Jenky\Atlas;

abstract class ConnectorlessRequest extends Request
{
    /**
     * Send the request.
     */
    public function send(): Response
    {
        return (new NullConnector())->send($this);
    }
}
