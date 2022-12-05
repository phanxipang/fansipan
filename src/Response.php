<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use ArrayAccess;
use Illuminate\Support\Traits\Macroable;
use LogicException;
use Psr\Http\Message\ResponseInterface;

/**
 * @method mixed dto()
 *
 * @mixin \Psr\Http\Message\ResponseInterface
 */
class Response implements ArrayAccess
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @var callable
     */
    protected $decoder;

    /**
     * The decoded response.
     *
     * @var array
     */
    protected $decoded;

    /**
     * Create new response instance.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return void
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * Get the decoded body of the response as an array or scalar value.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function data($key = null, $default = null)
    {
        if (! $this->decoded) {
            $this->decoded = $this->decode();
        }

        if (is_null($key)) {
            return $this->decoded;
        }

        return data_get($this->decoded, $key, $default);
    }

    /**
     * Set the decoder.
     *
     * @param  callable  $decoder
     * @return void
     */
    public function decoder(callable $decoder): void
    {
        $this->decoder = $decoder;
    }

    /**
     * Decode the response body.
     *
     * @return array
     */
    protected function decode(): array
    {
        $decoder = $this->decoder ?: function (Response $response) {
            return [];
        };

        return $decoder($this);
    }

    /**
     * Get a header from the response.
     *
     * @param  string  $header
     * @return null|string
     */
    public function header(string $header): ?string
    {
        return $this->response->getHeaderLine($header) ?: null;
    }

    /**
     * Get the headers from the response.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Get the status code of the response.
     *
     * @return int
     */
    public function status(): int
    {
        return (int) $this->response->getStatusCode();
    }

    /**
     * Get the reason phrase of the response.
     *
     * @return string
     */
    public function reason(): string
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * Determine if the request was successful.
     *
     * @return bool
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     *
     * @return bool
     */
    public function ok(): bool
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response was a redirect.
     *
     * @return bool
     */
    public function redirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * Determine if the response was a 401 "Unauthorized" response.
     *
     * @return bool
     */
    public function unauthorized(): bool
    {
        return $this->status() === 401;
    }

    /**
     * Determine if the response was a 403 "Forbidden" response.
     *
     * @return bool
     */
    public function forbidden(): bool
    {
        return $this->status() === 403;
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     *
     * @return bool
     */
    public function failed(): bool
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     *
     * @return bool
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     *
     * @return bool
     */
    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    /**
     * Execute the given callback if there was a server or client error.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function onError(callable $callback)
    {
        if ($this->failed()) {
            $callback($this);
        }

        return $this;
    }

    /**
     * Close the stream and any underlying resources.
     *
     * @return $this
     */
    public function close()
    {
        $this->response->getBody()->close();

        return $this;
    }

    /**
     * Get the underlying PSR response for the response.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function toPsrResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Create an exception if a server or client error occurred.
     *
     * @return \Illuminate\Http\Client\RequestException|null
     */
    public function toException()
    {
        if ($this->failed()) {
            return new RequestException($this);
        }
    }

    /**
     * Throw an exception if a server or client error occurred.
     *
     * @param  \Closure|null  $callback
     * @return $this
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function throw()
    {
        $callback = func_get_args()[0] ?? null;

        if ($this->failed()) {
            throw tap($this->toException(), function ($exception) use ($callback) {
                if ($callback && is_callable($callback)) {
                    $callback($this, $exception);
                }
            });
        }

        return $this;
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data()[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data()[$offset];
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetUnset($offset): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->body();
    }

    /**
     * Dynamically proxy other methods to the underlying response.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return static::hasMacro($method)
            ? $this->macroCall($method, $parameters)
            : $this->response->{$method}(...$parameters);
    }
}
