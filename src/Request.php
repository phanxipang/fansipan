<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Illuminate\Support\Traits\ForwardsCalls;
use Jenky\Atlas\Contracts\PendingRequest as PendingRequestInterface;

/**
 * @method \Jenky\Atlas\Response send()
 */
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
    protected function defaultQuery(): array
    {
        return [];
    }

    /**
     * Get request headers.
     *
     * @return array
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Get request body payload.
     *
     * @return array
     */
    protected function defaultPayload(): array
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

    /**
     * Get request query string parameters.
     *
     * @return \Jenky\Atlas\Map
     */
    public function query(): Map
    {
        if (is_null($this->query)) {
            $this->query = new Map($this->defaultQuery());
        }

        return $this->query;
    }

    /**
     * Get request headers.
     *
     * @return \Jenky\Atlas\Map
     */
    public function headers(): Map
    {
        if (is_null($this->headers)) {
            $this->headers = new Map($this->defaultHeaders());
        }

        return $this->headers;
    }

    /**
     * Get request payload.
     *
     * @return \Jenky\Atlas\Payload
     */
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
     * @return \Jenky\Atlas\Contracts\PendingRequest
     */
    protected function createPendingRequest(): PendingRequestInterface
    {
        return new PendingRequest($this);
    }

    /**
     * Dynamically pass calls to the pending request.
     *
     * @param  mixed  $method
     * @param  mixed  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo(
            $this->createPendingRequest(), $method, $parameters
        );
    }
}
