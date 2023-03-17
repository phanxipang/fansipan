<?php

declare(strict_types=1);

namespace Jenky\Atlas\Retry;

use Jenky\Atlas\Contracts\DelayStrategyInterface;

class Backoff implements DelayStrategyInterface
{
    /**
     * @var int[]
     */
    private $backoff;

    /**
     * @var int
     */
    private $fallbackDelayMs;

    public function __construct(array $backoff, int $fallbackDelayMs = 1000)
    {
        $this->backoff = $backoff;
        $this->fallbackDelayMs = $fallbackDelayMs;
    }

    public function delayFor(int $attempts): int
    {
        return $this->backoff[$attempts - 1] ?? $this->fallbackDelayMs;
    }
}
