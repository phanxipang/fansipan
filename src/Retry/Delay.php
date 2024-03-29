<?php

declare(strict_types=1);

namespace Fansipan\Retry;

use Fansipan\Contracts\DelayStrategyInterface;
use InvalidArgumentException;

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
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException(sprintf('Delay must be greater than or equal to zero: "%s" given.', $delayMs));
            // @codeCoverageIgnoreEnd
        }

        $this->delayMs = $delayMs;

        if ($multiplier < 1) {
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException(sprintf('Multiplier must be greater than or equal to one: "%s" given.', $multiplier));
            // @codeCoverageIgnoreEnd
        }

        $this->multiplier = $multiplier;

        if ($maxDelayMs < 0) {
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException(sprintf('Max delay must be greater than or equal to zero: "%s" given.', $maxDelayMs));
            // @codeCoverageIgnoreEnd
        }

        $this->maxDelayMs = $maxDelayMs;
    }

    public function delayFor(int $attempt): int
    {
        $delay = $this->delayMs * $this->multiplier ** $attempt;

        if ($delay > $this->maxDelayMs && $this->maxDelayMs > 0) {
            return $this->maxDelayMs;
        }

        return (int) $delay;
    }
}
