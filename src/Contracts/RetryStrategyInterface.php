<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Retry\RetryContext;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RetryStrategyInterface
{
    /**
     * Returns whether the request should be retried.
     */
    public function shouldRetry(RequestInterface $request, ResponseInterface $response): bool;

    /**
     * Returns the time to wait in milliseconds.
     */
    public function delay(RetryContext $context): int;
}
