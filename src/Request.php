<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Support\ForwardsCalls;

abstract class Request
{
    use ForwardsCalls;

    /**
     * The connector instance.
     *
     * @var null|\Jenky\Atlas\Connector
     */
    protected $connector;

    /**
     * @var \Jenky\Atlas\Map
     */
    protected $headers;

    /**
     * @var \Jenky\Atlas\Map
     */
    protected $query;

    /**
     * @var \Jenky\Atlas\Payload
     */
    protected $payload;

    /**
     * Get the request endpoint.
     *
     * @return string
     */
    abstract public function endpoint(): string;

    /**
     * Get request query string parameters.
     *
     * @return array
     */
    public function defaultQuery(): array
    {
        return [];
    }

    /**
     * Get request headers.
     *
     * @return array
     */
    public function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Get request body payload.
     *
     * @return array
     */
    public function defaultPayload(): array
    {
        return [];
    }

    /**
     * Get request HTTP method.
     *
     * @return string
     */
    public function method(): string
    {
        return property_exists($this, 'method') ? $this->method : 'GET';
    }

    public function query(): Map
    {
        if (is_null($this->query)) {
            $this->query = new Map($this->defaultQuery());
        }

        return $this->query;
    }

    public function headers(): Map
    {
        if (is_null($this->headers)) {
            $this->headers = new Map($this->defaultHeaders());
        }

        return $this->headers;
    }

    public function payload(): Payload
    {
        if (is_null($this->payload)) {
            $this->payload = new Payload($this->defaultPayload());
        }

        return $this->payload;
    }

    /**
     * Set the connector.
     *
     * @param  string|\Jenky\Atlas\Connector  $connector
     * @return $this
     */
    public function withConnector($connector)
    {
        $this->connector = $connector;

        return $this;
    }

    public function connector()
    {
        return $this->connector;
    }

    /**
     * Create a pending request instance.
     *
     * @return \Jenky\Atlas\PendingRequest
     */
    protected function createPendingRequest(): PendingRequest
    {
        return new PendingRequest($this);
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo(
            $this->createPendingRequest(), $method, $parameters
        );
    }
}
