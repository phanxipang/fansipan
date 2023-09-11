<?php

declare(strict_types=1);

namespace Jenky\Atlas\Retry;

use Jenky\Atlas\Exception\RequestRetryFailedException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class RetryContext
{
    /**
     * @var ClientInterface
     */
    private $client;

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
        ClientInterface $client,
        int $maxRetries = 3,
        bool $throw = true
    ) {
        $this->client = $client;
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

    public function pause(int $delayMs): void
    {
        if (\method_exists($this->client, 'pause')) {
            $this->client->pause($delayMs);
        }

        \usleep($delayMs * 1000);
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
