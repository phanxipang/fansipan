<?php

declare(strict_types=1);

namespace Jenky\Atlas;

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
     * Get request HTTP method.
     */
    public function method(): string
    {
        return 'GET';
    }

    /**
     * Get request HTTP protocol version.
     */
    public function version(): string
    {
        return '1.1';
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
        if (! $this->body instanceof PayloadInterface) {
            $this->body = $this->createPayload();
        }

        return $this->body;
    }

    /**
     * Create a corresponding payload for request body.
     */
    private function createPayload(): PayloadInterface
    {
        if (method_exists($this, 'definePayload')) {
            return $this->definePayload();
        }

        return new FormPayload(is_array($this->defaultBody()) ? $this->defaultBody() : []);
    }

    /**
     * Get the response decoder.
     */
    public function decoder(): DecoderInterface
    {
        $decoders = static function () {
            yield from [
                new JsonDecoder(),
                new XmlDecoder(),
            ];
        };

        return new ChainDecoder($decoders());
    }
}
