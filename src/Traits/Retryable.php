<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Middleware\RetryRequest;

trait Retryable
{
    public function retry(int $maxRetries = 3, int $delayMs = 1000, ?callable $when = null)
    {
        $this->middleware()->push(new RetryRequest($maxRetries, $delayMs, $when));

        return $this;
    }
}
