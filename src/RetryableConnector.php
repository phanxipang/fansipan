<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryStrategyInterface;
use Jenky\Atlas\Middleware\RetryRequests;
use Jenky\Atlas\Retry\Delay;
use Jenky\Atlas\Retry\GenericRetryStrategy;
use Jenky\Atlas\Traits\ConnectorDecoratorTrait;

class RetryableConnector implements ConnectorInterface
{
    use ConnectorDecoratorTrait;

    public function __construct(
        ConnectorInterface $connector,
        int $maxRetries = 3,
        ?RetryStrategyInterface $retryStrategy = null,
        bool $throw = true
    ) {
        $clone = clone $connector;
        $strategy = $retryStrategy ?: new GenericRetryStrategy(new Delay(1000, 2.0));

        $clone->middleware()
            ->unshift(new RetryRequests(
                $strategy,
                $maxRetries,
                $throw
            ));

        $this->connector = $clone;
    }
}
