<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use InvalidArgumentException;
use Jenky\Atlas\Body\FormPayload;
use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Contracts\PayloadInterface;
use Jenky\Atlas\Decoder\ChainDecoder;
use Jenky\Atlas\Decoder\JsonDecoder;
use Jenky\Atlas\Decoder\XmlDecoder;

abstract class Request
{
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

        return new $payload(is_array($this->defaultBody()) ? $this->defaultBody() : []);
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
        if (! $this->query instanceof Map) {
            $this->query = new Map($this->defaultQuery());
        }

        return $this->query;
    }

    /**
     * Get request headers.
     */
    public function headers(): Map
    {
        if (! $this->headers instanceof Map) {
            $this->headers = new Map($this->defaultHeaders());
        }

        return $this->headers;
    }

    /**
     * Get request payload.
     */
    public function body(): PayloadInterface
    {
        if (! $this->body instanceof Map) {
            $this->body = $this->definePayload();
        }

        return $this->body;
    }

    /**
     * Get the response decoder.
     */
    public function decoder(): DecoderInterface
    {
        return new ChainDecoder(
            new JsonDecoder(),
            new XmlDecoder()
        );
    }
}
