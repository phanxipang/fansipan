<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Request;

class GetStatusRequest extends Request
{
    protected $connector = Connector::class;

    private $status;

    public function __construct(int $status = 200)
    {
        $this->status = $status;
    }

    public function endpoint(): string
    {
        return '/status/'.$this->status;
    }
}
