<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Exceptions\RetryException;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Jenky\Atlas\Retry\RetryContext;

final class RetryRequest
{
    /**
     * @var \Jenky\Atlas\Retry\RetryContext
     */
    private $context;

    /**
     * @var \Jenky\Atlas\Contracts\RetryStrategyInterface
     */
    private $retryStrategy;

    public function __construct(RetryContext $context, RetryStrategyInterface $retryStrategy)
    {
        $this->context = $context;
        $this->retryStrategy = $retryStrategy;
    }

    public function __invoke(Request $request, Closure $next): Response
    {
        $this->context->attempting();

        $response = $next($request);

        if ($this->retryStrategy->shouldRetry($request, $response)) {
            throw new RetryException(
                $request,
                $response,
                $this->context,
                $this->getDelayFromHeaders($response) ?? $this->retryStrategy->delay($this->context)
            );
        }

        return $response;
    }

    /**
     * Get the delay from Retry-After header if present.
     */
    private function getDelayFromHeaders(Response $response): ?int
    {
        $after = $response->header('Retry-After');

        if ($after) {
            if (is_numeric($after)) {
                return (int) ($after * 1000);
            }

            if ($time = strtotime($after)) {
                return max(0, $time - time()) * 1000;
            }
        }

        return null;
    }
}
