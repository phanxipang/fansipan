<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use ArrayAccess;
use Closure;
use Jenky\Atlas\Exceptions\HttpException;
use LogicException;
use Psr\Http\Message\ResponseInterface;

/**
 * @method mixed dto()
 *
 * @mixin \Psr\Http\Message\ResponseInterface
 */
class Response implements ArrayAccess
{
    use Traits\Macroable {
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
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * Get the decoded body of the response as an array or scalar value.
     *
     * @return mixed
     */
    public function data()
    {
        if (! $this->decoded) {
            $this->decoded = $this->decode();
        }

        return $this->decoded;
    }

    /**
     * Set the decoder.
     */
    public function decoder(callable $decoder): void
    {
        $this->decoder = $decoder;
    }

    /**
     * Decode the response body.
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
     */
    public function header(string $header): ?string
    {
        return $this->response->getHeaderLine($header) ?: null;
    }

    /**
     * Get the headers from the response.
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Get the status code of the response.
     */
    public function status(): int
    {
        return (int) $this->response->getStatusCode();
    }

    /**
     * Get the reason phrase of the response.
     */
    public function reason(): string
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * Determine if the request was successful.
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     */
    public function ok(): bool
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response was a redirect.
     */
    public function redirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * Determine if the response was a 401 "Unauthorized" response.
     */
    public function unauthorized(): bool
    {
        return $this->status() === 401;
    }

    /**
     * Determine if the response was a 403 "Forbidden" response.
     */
    public function forbidden(): bool
    {
        return $this->status() === 403;
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     */
    public function failed(): bool
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     */
    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    /**
     * Execute the given callback if there was a server or client error.
     */
    public function onError(callable $callback): self
    {
        if ($this->failed()) {
            $callback($this);
        }

        return $this;
    }

    /**
     * Close the stream and any underlying resources.
     */
    public function close(): self
    {
        $this->response->getBody()->close();

        return $this;
    }

    /**
     * Get the underlying PSR response for the response.
     */
    public function toPsrResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Create an exception if a server or client error occurred.
     */
    public function toException(): ?HttpException
    {
        if ($this->clientError()) {
            return new Exceptions\ClientException($this);
        }

        if ($this->serverError()) {
            return new Exceptions\ServerException($this);
        }

        return null;
    }

    /**
     * Throw an exception if a server or client error occurred.
     *
     * @throws \Jenky\Atlas\Exceptions\HttpException
     */
    public function throw(): self
    {
        $callback = func_get_args()[0] ?? null;

        if ($this->failed()) {
            $exception = $this->toException();

            if ($callback && is_callable($callback)) {
                $callback($this, $exception);
            }

            throw $exception;
        }

        return $this;
    }

    /**
     * Throw an exception if a server or client error occurred and the given condition evaluates to true.
     *
     * @throws \Jenky\Atlas\Exceptions\HttpException
     */
    public function throwIf($condition): self
    {
        $condition = $condition instanceof Closure ? $condition($this) : $condition;

        return $condition ? $this->throw(func_get_args()[1] ?? null) : $this;
    }

    /**
     * Determine if the given offset exists.
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
     * @throws \LogicException
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Unset the value at the given offset.
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
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $result = $this->response->{$method}(...$parameters);

        if ($result instanceof ResponseInterface) {
            // Allow to modify response
            $this->response = $result;
        }

        return $this;
    }
}
