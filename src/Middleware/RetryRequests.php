<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Exception\RequestRetryFailedException;
use Jenky\Atlas\Retry\RetryContext;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RetryRequests
{
    /**
     * @var RetryContext
     */
    private $context;

    /**
     * @var RetryStrategyInterface
     */
    private $strategy;

    public function __construct(
        RetryStrategyInterface $strategy,
        RetryContext $context
    ) {
        $this->context = $context;
        $this->strategy = $strategy;
    }

    /**
     * @throws RequestRetryFailedException
     */
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $response = $next($request);

        if (! $this->strategy->shouldRetry($request, $response)) {
            return $response;
        }

        $this->context->attempting();

        if ($this->context->shouldStop()) {
            $this->context->throwExceptionIfNeeded($request, $response);

            return $response;
        }

        $delay = $this->getDelayFromHeaders($response) ?? $this->strategy->delay($this->context);

        if ($delay > 0) {
            $this->context->pause($delay);
        }

        return $this($request, $next);
    }

    /**
     * Get the delay from Retry-After header if present.
     */
    private function getDelayFromHeaders(ResponseInterface $response): ?int
    {
        $after = $response->getHeaderLine('Retry-After');

        if ($after) {
            if (\is_numeric($after)) {
                return (int) ($after * 1000);
            }

            if ($time = \strtotime($after)) {
                return \max(0, $time - \time()) * 1000;
            }
        }

        return null;
    }
}
