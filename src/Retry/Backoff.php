<?php

declare(strict_types=1);

namespace Fansipan\Retry;

use Fansipan\Contracts\DelayStrategyInterface;

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

    public function delayFor(int $attempt): int
    {
        return $this->backoff[$attempt - 1] ?? $this->fallbackDelayMs;
    }
}
