<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Request;

final class GetStatusRequest extends Request
{
    private $status;

    public function __construct(int $status = 200)
    {
        $this->status = $status;
    }

    public function withStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function endpoint(): string
    {
        return '/status/'.$this->status;
    }
}
