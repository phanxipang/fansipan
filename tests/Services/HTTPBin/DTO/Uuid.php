<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin\DTO;

class Uuid
{
    protected $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }
}
