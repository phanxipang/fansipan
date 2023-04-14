<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Exceptions\RetryException;
use Jenky\Atlas\Retry\RetryContext;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

    /**
     * @throws \Jenky\Atlas\Exceptions\RetryException
     */
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
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
    private function getDelayFromHeaders(ResponseInterface $response): ?int
    {
        $after = $response->getHeaderLine('Retry-After');

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
