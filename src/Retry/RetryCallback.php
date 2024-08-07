<?php

declare(strict_types=1);

namespace Fansipan\Retry;

use Closure;
use Fansipan\Contracts\DelayStrategyInterface;
use Fansipan\Contracts\RetryStrategyInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RetryCallback implements RetryStrategyInterface
{
    /**
     * @var \Closure(RequestInterface, ResponseInterface): bool
     */
    private $when;

    /**
     * @var \Closure(RetryContext): int
     */
    private $delay;

    /**
     * @param  \Closure(RequestInterface, ResponseInterface): bool $when
     * @param  \Closure(RetryContext): int $delay
     */
    public function __construct(Closure $when, Closure $delay)
    {
        $this->when = $when;
        $this->delay = $delay;
    }

    /**
     * Determines if the retries should actually be attempted.
     */
    public static function when(Closure $callback, int $delay = 1000, float $multiplier = 1.0): self
    {
        return new self($callback, static function (RetryContext $context) use ($delay, $multiplier) {
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

        $clone->delay = static function (RetryContext $context) use ($delay) {
            return $delay->delayFor($context->attempts());
        };

        return $clone;
    }

    public function shouldRetry(RequestInterface $request, ResponseInterface $response): bool
    {
        return ($this->when)($request, $response);
    }

    public function delay(RetryContext $context): int
    {
        return ($this->delay)($context);
    }
}
