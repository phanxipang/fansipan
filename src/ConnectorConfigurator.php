<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Middleware\FollowRedirects;
use Jenky\Atlas\Middleware\RetryRequests;
use Jenky\Atlas\Retry\Delay;
use Jenky\Atlas\Retry\GenericRetryStrategy;
use Jenky\Atlas\Retry\RetryContext;

/**
 * @template T of ConnectorInterface
 */
class ConnectorConfigurator
{
    /**
     * @var array
     */
    private $handlers = [];

    /**
     * Configure the given connector with options for current request.
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
     * Register a configuration handler.
     *
     * @param  \Closure(T): void $handler
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
        return $this->register(function (ConnectorInterface $connector) use ($retryStrategy, $maxRetries, $throw) {
            $strategy = $retryStrategy ?? new GenericRetryStrategy(new Delay(1000, 2.0));
            $connector->middleware()->unshift(new RetryRequests(
                $strategy, new RetryContext(
                    $connector->client(),
                    $maxRetries,
                    $throw
                )
            ), 'retry_requests');
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
        return $this->register(function (ConnectorInterface $connector) use ($max, $protocols, $strict, $referer) {
            $connector->middleware()->unshift(new FollowRedirects(
                $max,
                $protocols,
                $strict,
                $referer
            ), 'follow_redirects');
        });
    }
}
