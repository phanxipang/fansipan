<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Middleware\RetryRequest;
use Jenky\Atlas\Retry\Delay;
use Jenky\Atlas\Retry\GenericRetryStrategy;
use Jenky\Atlas\Retry\RetryContext;

trait Retryable
{
    public function retry(int $maxRetries = 3, ?RetryStrategyInterface $retryStrategy = null, bool $throw = true): ConnectorInterface
    {
        $clone = clone $this;

        if ($clone->middleware()->has('retry_request')) {
            return $clone;
        }

        $clone->middleware()->unshift(new RetryRequest(
            new RetryContext($maxRetries, $throw),
            $retryStrategy ?? $this->defaultRetryStrategy()
        ), 'retry_request');

        return $clone;
    }

    /**
     * Get the default retry strategy.
     */
    protected function defaultRetryStrategy(): RetryStrategyInterface
    {
        return new GenericRetryStrategy(new Delay(1000, 2.0));
    }
}
