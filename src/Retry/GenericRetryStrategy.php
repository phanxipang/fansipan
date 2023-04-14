<?php

declare(strict_types=1);

namespace Jenky\Atlas\Retry;

use Jenky\Atlas\Contracts\DelayStrategyInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class GenericRetryStrategy implements RetryStrategyInterface
{
    public const IDEMPOTENT_METHODS = ['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'];

    public const DEFAULT_RETRY_STATUS_CODES = [
        423,
        425,
        429,
        500 => self::IDEMPOTENT_METHODS,
        502,
        503,
        504 => self::IDEMPOTENT_METHODS,
        507 => self::IDEMPOTENT_METHODS,
        510 => self::IDEMPOTENT_METHODS,
    ];

    /**
     * @var array
     */
    private $statusCodes;

    /**
     * @var \Jenky\Atlas\Contracts\DelayStrategyInterface
     */
    private $delayStrategy;

    public function __construct(
        DelayStrategyInterface $delayStrategy,
        array $statusCodes = self::DEFAULT_RETRY_STATUS_CODES
    ) {
        $this->delayStrategy = $delayStrategy;
        $this->statusCodes = $statusCodes;
    }

    public function shouldRetry(RequestInterface $request, ResponseInterface $response): bool
    {
        $status = $response->getStatusCode();

        if (in_array($status, $this->statusCodes, true)) {
            return true;
        }

        if (isset($this->statusCodes[$status]) && is_array($this->statusCodes[$status])) {
            return in_array(mb_strtoupper($request->getMethod()), $this->statusCodes[$status], true);
        }

        return false;
    }

    public function delay(RetryContext $context): int
    {
        return $this->delayStrategy->delayFor($context->attempts());
    }
}
