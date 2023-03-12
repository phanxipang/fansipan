<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface RetryableInterface
{
    /**
     * Specify the number of times the request should be attempted.
     *
     * @return $this
     */
    public function retry(int $maxRetries = 3, int $delayMs = 1000, ?callable $when = null);
}
