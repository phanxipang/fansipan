<?php

declare(strict_types=1);

namespace Fansipan;

abstract class ConnectorlessRequest extends Request
{
    /**
     * Send the request.
     */
    final public function send(): Response
    {
        return (new GenericConnector())->send($this);
    }
}
