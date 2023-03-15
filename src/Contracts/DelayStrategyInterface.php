<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface DelayStrategyInterface
{
    public function delayFor(int $attempts): int;
}
