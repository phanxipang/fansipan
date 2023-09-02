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
     * @var \Jenky\Atlas\Retry\RetryContext
     */
    private $context;

    /**
     * @var \Jenky\Atlas\Contracts\RetryStrategyInterface
     */
    private $strategy;

    /**
     * @var callable(int $delayMs): void
     */
    private static $pauserHandler;

    public function __construct(
        RetryStrategyInterface $strategy,
        int $maxRetries = 3,
        bool $throw = true
    ) {
        $this->context = new RetryContext($maxRetries, $throw);
        $this->strategy = $strategy;

        if (! \is_callable(self::$pauserHandler)) {
            self::$pauserHandler = static function (int $delay): void {
                \usleep($delay * 1000);
            };
        }
    }

    /**
     * @throws \Jenky\Atlas\Exception\RequestRetryFailedException
     */
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $response = $next($request);

        if (! $this->strategy->shouldRetry($request, $response)) {
            return $response;
        }

        $this->context->attempting();
        $stop = $this->context->maxRetries() < $this->context->attempts();

        if ($stop) {
            if ($this->context->throwable()) {
                throw new RequestRetryFailedException(
                    \sprintf('Maximum %d retries reached.', $this->context->maxRetries()),
                    $request,
                    $response
                );
            }

            return $response;
        }

        $delay = $this->getDelayFromHeaders($response) ?? $this->strategy->delay($this->context);

        if ($delay > 0) {
            (self::$pauserHandler)($delay);
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
