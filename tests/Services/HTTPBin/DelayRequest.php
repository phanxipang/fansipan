<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Request;

final class DelayRequest extends Request
{
    private $delay = 0;

    public function __construct(int $delay = 0)
    {
        $this->delay = $delay;
    }

    public function endpoint(): string
    {
        return '/delay/'.$this->delay;
    }
}
