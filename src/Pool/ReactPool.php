<?php

declare(strict_types=1);

namespace Jenky\Atlas\Pool;

use Jenky\Atlas\Contracts\PoolInterface;
use React\Async;

final class ReactPool implements PoolInterface
{
    /**
     * @var array<array-key, callable>
     */
    private $requests = [];

    public function queue($key, callable $request): void
    {
        $this->requests[$key] = Async\async($request);
    }

    public function send(): array
    {
        return Async\await(Async\parallel($this->requests));
    }
}
