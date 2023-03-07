<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Response;

class XmlDecoder
{
    public function __invoke(Response $response): array
    {
        $xml = simplexml_load_string($response->body());

        if (! $xml) {
            return [];
        }

        return json_decode(
            json_encode($xml) ?: '[]', true
        );
    }
}
