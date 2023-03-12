<?php

declare(strict_types=1);

namespace Jenky\Atlas\Middleware;

use Closure;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

final class RetryRequest
{
    private $maxRetries;

    private $delayMs;

    public function __construct(int $maxRetries = 3, int $delayMs = 1000, ?callable $when = null)
    {
        $this->maxRetries = $maxRetries;
        $this->delayMs = $delayMs;
    }

    public function __invoke(Request $request, Closure $next): Response
    {
        return $next($request);

        // if ($this->strategy->shouldRetry($request, $response)) {
        //     // Do retry
        // }
    }
}
