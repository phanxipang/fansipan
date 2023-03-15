<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Jenky\Atlas\Retry\RetryContext;

interface RetryStrategyInterface
{
    /**
     * Returns whether the request should be retried.
     */
    public function shouldRetry(Request $request, Response $response): bool;

    /**
     * Returns the time to wait in milliseconds.
     */
    public function delay(RetryContext $context): int;
}
