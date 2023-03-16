<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface RetryableInterface
{
    /**
     * Indicates that failed requests should be attempted again.
     *
     * It accepts the maximum number of times the request should be attempted and a
     * retry strategy to decide if the request should be retried, and to define
     * the waiting time between each retry.
     */
    public function retry(int $maxRetries = 3, ?RetryStrategyInterface $retryStrategy = null): ConnectorInterface;
}
