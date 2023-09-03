<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Middleware\FollowRedirects;
use Jenky\Atlas\Middleware\RetryRequests;
use Jenky\Atlas\Retry\Delay;
use Jenky\Atlas\Retry\GenericRetryStrategy;

class ConnectorConfigurator
{
    /**
     * @var array<array-key, callable(\Psr\Http\Message\RequestInterface, callable): \Psr\Http\Message\ResponseInterface>
     */
    protected $middleware = [];

    /**
     * Configure the given connector with options for current request.
     *
     * @template T of ConnectorInterface
     * @param  T $connector
     * @return T
     */
    final public function configure(ConnectorInterface $connector): ConnectorInterface
    {
        if (empty($this->middleware)) {
            return $connector;
        }

        $clone = clone $connector;

        foreach ($this->middleware as $name => $middleware) {
            $clone->middleware()->unshift($middleware, \is_string($name) ? $name : '');
        }

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
        $strategy = $retryStrategy ?: new GenericRetryStrategy(new Delay(1000, 2.0));

        $clone = clone $this;

        $clone->middleware['retry_requests'] = new RetryRequests(
            $strategy,
            $maxRetries,
            $throw
        );

        return $clone;
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
        $clone = clone $this;

        $clone->middleware['follow_redirects'] = new FollowRedirects(
            $max,
            $protocols,
            $strict,
            $referer
        );

        return $clone;
    }
}
