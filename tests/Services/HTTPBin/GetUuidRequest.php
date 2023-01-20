<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Contracts\DtoSerializable;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\HTTPBin\DTO\Uuid;

class GetUuidRequest extends Request implements DtoSerializable
{
    public function endpoint(): string
    {
        return '/uuid';
    }

    public function toDto(Response $response): object
    {
        return new Uuid($response->data()['uuid'] ?? '');
    }
}
