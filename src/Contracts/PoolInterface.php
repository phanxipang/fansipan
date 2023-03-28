<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface PoolInterface
{
    /**
     * Put the request to the queue to be sent later.
     *
     * @param  array-key $key
     * @param  callable(): \Jenky\Atlas\Response $request
     */
    public function queue($key, callable $request): void;

    /**
     * Send concurrent requests.
     *
     * @return array<array-key, \Jenky\Atlas\Response>
     */
    public function send(): array;
}
