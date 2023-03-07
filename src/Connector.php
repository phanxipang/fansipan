<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Connector implements ConnectorInterface, ClientInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;

    /**
     * Assign connector to given request.
     */
    public function request(Request $request): Request
    {
        return $request->withConnector($this);
    }

    public function send(Request $request): Response
    {
        return PendingRequest::from($this->request($request))->send();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client()->sendRequest($request);
    }
}
