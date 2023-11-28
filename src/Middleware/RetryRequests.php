<?php

declare(strict_types=1);

namespace Fansipan\Middleware;

use Fansipan\Contracts\RetryStrategyInterface;
use Fansipan\Exception\RequestRetryFailedException;
use Fansipan\Retry\RetryContext;
use Psr\Http\Client\ClientInterface;
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

    /**
     * @var ClientInterface|null
     */
    private $client;

    public function __construct(
        RetryStrategyInterface $strategy,
        int $maxRetries = 3,
        bool $throw = true
    ) {
        $this->context = new RetryContext($maxRetries, $throw);
        $this->strategy = $strategy;
    }

    /**
     * Set the client.
     */
    public function withClient(ClientInterface $client): self
    {
        $clone = clone $this;
        $clone->client = $client;

        return $clone;
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
            if ($this->client !== null && \method_exists($this->client, 'delay')) {
                $this->client->delay($delay);
            } else {
                \usleep($delay * 1000);
            }
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
