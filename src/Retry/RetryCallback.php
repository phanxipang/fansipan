<?php

declare(strict_types=1);

namespace Jenky\Atlas\Retry;

use Closure;
use Jenky\Atlas\Contracts\DelayStrategyInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

final class RetryCallback implements RetryStrategyInterface
{
    /**
     * @var \Closure
     */
    private $when;

    /**
     * @var \Closure
     */
    private $delay;

    public function __construct(Closure $when, Closure $delay)
    {
        $this->when = $when;
        $this->delay = $delay;
    }

    public static function when(Closure $callback, int $delay = 1000, float $multiplier = 1.0): self
    {
        return new self($callback, function (RetryContext $context) use ($delay, $multiplier) {
            return (new Delay($delay, $multiplier))->delayFor($context->attempts());
        });
    }

    /**
     * Set the delay strategy.
     *
     * @return static
     */
    public function withDelay(DelayStrategyInterface $delay)
    {
        $clone = clone $this;

        $clone->delay = function (RetryContext $context) use ($delay) {
            return $delay->delayFor($context->attempts());
        };

        return $clone;
    }

    public function shouldRetry(Request $request, Response $response): bool
    {
        return ($this->when)($request, $response);
    }

    public function delay(RetryContext $context): int
    {
        return ($this->delay)($context);
    }
}
