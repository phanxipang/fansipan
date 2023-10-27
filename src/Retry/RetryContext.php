<?php

declare(strict_types=1);

namespace Fansipan\Retry;

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

    public function maxRetries(): int
    {
        return $this->maxRetries;
    }

    public function attempts(): int
    {
        return $this->attempts;
    }

    public function throwable(): bool
    {
        return $this->throw;
    }
}
