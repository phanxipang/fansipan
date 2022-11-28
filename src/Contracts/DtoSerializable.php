<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Response;

interface DtoSerializable
{
    public function toDto(Response $response);
}
