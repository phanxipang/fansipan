<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Http\Discovery\Psr18ClientDiscovery;
use Jenky\Atlas\Contracts\ConnectorInterface;
use Psr\Http\Client\ClientInterface;

trait HasClient
{
    /**
     * The HTTP client instance.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * Return the instance with provided HTTP client.
     *
     * @return static
     */
    public function withClient(ClientInterface $client): ConnectorInterface
    {
        $clone = clone $this;

        $clone->client = $client;

        return $clone;
    }

    /**
     * Get the HTTP client instance.
     */
    public function client(): ClientInterface
    {
        if (! $this->client instanceof ClientInterface) {
            $this->client = $this->defaultClient();
        }

        return $this->client;
    }

    /**
     * Define the default HTTP client instance.
     */
    protected function defaultClient(): ClientInterface
    {
        return Psr18ClientDiscovery::find();
    }
}
