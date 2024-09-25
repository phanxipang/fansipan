<?php

declare(strict_types=1);

namespace Fansipan;

use Fansipan\Contracts\AuthenticatorInterface;
use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Contracts\RetryStrategyInterface;
use Fansipan\Middleware\Authentication;
use Fansipan\Middleware\FollowRedirects;
use Fansipan\Middleware\RetryRequests;
use Fansipan\Retry\Delay;
use Fansipan\Retry\GenericRetryStrategy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ConnectorConfigurator
{
    /**
     * @var array<\Closure(ConnectorInterface): void>
     */
    private $handlers = [];

    /**
     * Configure the given connector with options for current request.
     *
     * @template T of ConnectorInterface
     *
     * @param  T $connector
     * @return T
     */
    final public function configure(ConnectorInterface $connector): ConnectorInterface
    {
        $clone = clone $connector;

        foreach ($this->handlers as $handler) {
            $handler($clone);
        }

        return $clone;
    }

    /**
     * Register a middleware configuration handler.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface $middleware
     * @return static
     */
    final public function middleware(callable $middleware, string $name = '')
    {
        return $this->register(static function (ConnectorInterface $connector) use ($middleware, $name) {
            $connector->middleware()->unshift(
                $middleware, $name
            );
        });
    }

    /**
     * Register a configuration handler.
     *
     * @param  \Closure(ConnectorInterface): void $handler
     * @return static
     */
    protected function register(\Closure $handler)
    {
        $clone = clone $this;
        $clone->handlers[] = $handler;

        return $clone;
    }

    /**
     * Indicate that a failed request should be retried.
     *
     * @return static
     */
    public function retry(
        int $maxRetries = 3,
        ?RetryStrategyInterface $retryStrategy = null,
        bool $throw = true
    ) {
        return $this->register(static function (ConnectorInterface $connector) use ($retryStrategy, $maxRetries, $throw) {
            $strategy = $retryStrategy ?? new GenericRetryStrategy(new Delay(1000, 2.0));
            $middleware = new RetryRequests($strategy, $maxRetries, $throw);
            $connector->middleware()->unshift(
                $middleware, 'retry_requests'
            );
        });
    }

    /**
     * Indicate that redirects should be followed for current request.
     *
     * @param  string[] $protocols
     * @return static
     */
    public function followRedirects(
        int $max = 5,
        array $protocols = ['http', 'https'],
        bool $strict = false,
        bool $referer = false
    ) {
        return $this->middleware(new FollowRedirects(
            $max,
            $protocols,
            $strict,
            $referer
        ), 'follow_redirects');
    }

    /**
     * Set the authenticator for current request.
     *
     * @return static
     */
    public function auth(AuthenticatorInterface $authenticator)
    {
        return $this->middleware(new Authentication($authenticator), 'authentication');
    }
}
