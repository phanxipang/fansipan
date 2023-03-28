<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Contracts\PoolInterface;
use Jenky\Atlas\Pool\AmphpPool;
use Jenky\Atlas\Pool\ReactPool;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

trait Poolable
{
    public function pool(iterable $requests, ?PoolInterface $pool = null): PoolInterface
    {
        $pool = $pool ?: $this->defaultPool();

        foreach ($requests as $key => $request) {
            if ($request instanceof Request) {
                $pool->queue($key, function () use ($request): Response {
                    return $this->send($request);
                });
            } elseif (is_callable($request)) {
                $pool->queue($key, $request);
            } else {
                throw new \InvalidArgumentException('Each value of the iterator must be a Jenky\Atlas\Request or a \Closure that returns a Jenky\Atlas\Response object.');
            }
        }

        return $pool;
    }

    /**
     * Get default pool instance.
     */
    protected function defaultPool(): PoolInterface
    {
        $candidates = [
            ReactPool::class => function (): bool {
                return function_exists('React\\Async\\async')
                    && function_exists('React\\Async\\await')
                    && function_exists('React\\Async\\parallel');
            },
            AmphpPool::class => function (): bool {
                return function_exists('Amp\\async')
                    && function_exists('Amp\\Future\awaitAll');
            },
        ];

        foreach ($candidates as $pool => $condition) {
            if ($condition()) {
                return new $pool();
            }
        }

        throw new \LogicException('You cannot use the pooling as the "amphp/parallel" or "react/async" package is not installed.');
    }
}
