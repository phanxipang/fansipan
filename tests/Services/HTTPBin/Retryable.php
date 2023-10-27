<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Contracts\RetryStrategyInterface;
use Fansipan\Middleware\RetryRequests;
use Fansipan\Retry\Delay;
use Fansipan\Retry\GenericRetryStrategy;

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
