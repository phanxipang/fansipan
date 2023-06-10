<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Middleware\RetryRequests;
use Jenky\Atlas\Retry\Delay;
use Jenky\Atlas\Retry\GenericRetryStrategy;

trait Retryable
{
    public function retry(int $maxRetries = 3, ?RetryStrategyInterface $retryStrategy = null, bool $throw = true): ConnectorInterface
    {
        $clone = clone $this;

        $clone->middleware()
            ->unshift(new RetryRequests(
                $retryStrategy ?? $this->defaultRetryStrategy(),
                $maxRetries,
                $throw
            ));

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
