<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin\DTO;

use Fansipan\Response;

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
