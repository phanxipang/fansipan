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
     * The request collection.
     *
     * @var array
     */
    protected $requests = [];

    /**
     * Assign connector to given request.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return \Jenky\Atlas\Request
     */
    public function request(Request $request): Request
    {
        return $request->withConnector($this);
    }

    /**
     * Send the request.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return \Jenky\Atlas\Response
     */
    public function send(Request $request): Response
    {
        return PendingRequest::from($this->request($request))->send();
    }
}
