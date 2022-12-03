<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;

trait HasClient
{
    /**
     * The HTTP client instance.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    protected $client;

    /**
     * Set the HTTP client instance.
     *
     * @param  \Psr\Http\Client\ClientInterface  $client
     * @return $this
     */
    public function withClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get and set the HTTP client instance.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    public function client(): ClientInterface
    {
        if (is_null($this->client)) {
            $this->client = $this->defineClient();
        }

        return $this->client;
    }

    /**
     * Define the default HTTP client instance.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    protected function defineClient(): ClientInterface
    {
        return Psr18ClientDiscovery::find();
    }
}
