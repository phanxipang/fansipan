<?php

declare(strict_types=1);

namespace Jenky\Atlas\Retry;

use InvalidArgumentException;
use Jenky\Atlas\Contracts\DelayStrategyInterface;

class Delay implements DelayStrategyInterface
{
    /**
     * @var int
     */
    private $delayMs;

    /**
     * @var float
     */
    private $multiplier;

    /**
     * @var int
     */
    private $maxDelayMs;

    public function __construct(int $delayMs = 1000, float $multiplier = 1.0, int $maxDelayMs = 0)
    {
        if ($delayMs < 0) {
            throw new InvalidArgumentException(sprintf('Delay must be greater than or equal to zero: "%s" given.', $delayMs));
        }

        $this->delayMs = $delayMs;

        if ($multiplier < 1) {
            throw new InvalidArgumentException(sprintf('Multiplier must be greater than or equal to one: "%s" given.', $multiplier));
        }

        $this->multiplier = $multiplier;

        if ($maxDelayMs < 0) {
            throw new InvalidArgumentException(sprintf('Max delay must be greater than or equal to zero: "%s" given.', $maxDelayMs));
        }

        $this->maxDelayMs = $maxDelayMs;
    }

    public function delayFor(int $attempts): int
    {
        $delay = $this->delayMs * $this->multiplier ** $attempts;

        if ($delay > $this->maxDelayMs && $this->maxDelayMs > 0) {
            return $this->maxDelayMs;
        }

        return (int) $delay;
    }
}
