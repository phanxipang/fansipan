<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface PoolableInterface
{
    /**
     * Create a new pool to send concurrent requests.
     *
     * @param  iterable<callable(): \Jenky\Atlas\Response> $requests
     */
    public function pool(iterable $requests): PoolInterface;
}
