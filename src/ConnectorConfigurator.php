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
            $clone->middleware()->unshift($middleware, is_string($name) ? $name : '');
        }

        return $clone;
    }

    /**
     * @return static
     */
    public function retry(
        int $maxRetries = 3,
        ?RetryStrategyInterface $retryStrategy = null,
        bool $throw = true
    ) {
        $strategy = $retryStrategy ?: new GenericRetryStrategy(new Delay(1000, 2.0));

        $clone = clone $this;

        $clone->middleware[] = new RetryRequests(
            $strategy,
            $maxRetries,
            $throw
        );

        return $clone;
    }

    public function followRedirects(
        int $max = 5,
        array $protocols = ['http', 'https'],
        bool $strict = false,
        bool $referer = false
    ) {
        $clone = clone $this;

        $clone->middleware[] = new FollowRedirects(
            $max,
            $protocols,
            $strict,
            $referer
        );

        return $clone;
    }
}
