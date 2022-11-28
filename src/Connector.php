<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Http\Factory\Discovery\HttpClient;
use Psr\Http\Client\ClientInterface;

class Connector
{
    /**
     * The HTTP client instance.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    protected $client;

    /**
     * Get and set the HTTP client instance.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    public function client(?ClientInterface $client = null): ClientInterface
    {
        if ($client) {
            $this->client = $client;
        }

        if (is_null($this->client)) {
            $this->client = $this->defineClient();
        }

        return $this->client;
    }

    /**
     * Define the HTTP client instance.
     *
     * @return \Psr\Http\Client\ClientInterface
     *
     * @throws \RuntimeException
     */
    protected function defineClient(): ClientInterface
    {
        return HttpClient::client();
    }

    public function middleware(): array
    {
        return [];
    }

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
