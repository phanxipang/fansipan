<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Response;

interface ResponseDecodable
{
    public function decodeResponse(Response $response): array;
}
