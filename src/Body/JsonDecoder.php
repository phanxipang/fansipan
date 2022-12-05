<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Response;

class JsonDecoder
{
    public function __invoke(Response $response): array
    {
        return json_decode($response->body(), true);
    }
}
