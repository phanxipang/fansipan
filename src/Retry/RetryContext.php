<?php

declare(strict_types=1);

namespace Fansipan\Retry;

use Fansipan\Exception\RequestRetryFailedException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class RetryContext
{
    /**
     * @var int
     */
    private $maxRetries;

    /**
     * @var int
     */
    private $attempts = 0;

    /**
     * @var bool
     */
    private $throw;

    public function __construct(
        int $maxRetries = 3,
        bool $throw = true
    ) {
        $this->maxRetries = $maxRetries;
        $this->throw = $throw;
    }

    public function attempting(): void
    {
        $this->attempts++;
    }

    public function attempts(): int
    {
        return $this->attempts;
    }

    public function shouldStop(): bool
    {
        return $this->maxRetries < $this->attempts;
    }

    /**
     * @throws RequestRetryFailedException
     */
    public function throwExceptionIfNeeded(RequestInterface $request, ResponseInterface $response): void
    {
        if (! $this->throw) {
            return;
        }

        throw new RequestRetryFailedException(
            \sprintf('Maximum %d retries reached.', $this->maxRetries),
            $request,
            $response
        );
    }
}
