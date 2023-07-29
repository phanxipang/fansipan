<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface DelayStrategyInterface
{
    /**
     * Returns the time to wait in milliseconds for given attempt.
     */
    public function delayFor(int $attempt): int;
}
