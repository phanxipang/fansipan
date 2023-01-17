<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use InvalidArgumentException;
use Jenky\Atlas\Body\FormPayload;
use Jenky\Atlas\Contracts\PayloadInterface;

abstract class Request
{
    /**
     * The connector instance.
     *
     * @var null|\Jenky\Atlas\Connector
     */
    protected $connector;

    /**
     * @var \Jenky\Atlas\Map
     */
    private $headers;

    /**
     * @var \Jenky\Atlas\Map
     */
    private $query;

    /**
     * @var \Jenky\Atlas\Contracts\PayloadInterface
     */
    private $body;

    /**
     * Get the request endpoint.
     */
    abstract public function endpoint(): string;

    /**
     * Get request query string parameters.
     */
    protected function defaultQuery(): array
    {
        return [];
    }

    /**
     * Get request headers.
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Get request body payload.
     *
     * @return mixed
     */
    protected function defaultBody()
    {
        return null;
    }

    /**
     * Create a body payload from body format.
     */
    protected function definePayload(): PayloadInterface
    {
        $payload = property_exists($this, 'bodyFormat') ? $this->bodyFormat : FormPayload::class;

        if (! is_a($payload, PayloadInterface::class, true)) {
            throw new InvalidArgumentException('Payload class must be instance of '.PayloadInterface::class);
        }

        return is_null($this->defaultBody()) ? new $payload() : new $payload($this->defaultBody());
    }

    /**
     * Get request HTTP method.
     */
    public function method(): string
    {
        return property_exists($this, 'method') ? $this->method : 'GET';
    }

    /**
     * Get request query string parameters.
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
     */
    public function body(): PayloadInterface
    {
        if (is_null($this->body)) {
            $this->body = $this->definePayload();
        }

        return $this->body;
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

    /**
     * Get the connector.
     *
     * @return null|string|\Jenky\Atlas\Connector
     */
    public function connector()
    {
        return $this->connector;
    }

    /**
     * Send the request.
     */
    public function send(): Response
    {
        return PendingRequest::from($this)->send();
    }
}
