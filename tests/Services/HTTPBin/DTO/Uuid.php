<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin\DTO;

use Jenky\Atlas\Response;

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

    public static function fromResponse(Response $response): self
    {
        return new self($response->data()['uuid'] ?? '');
    }
}
