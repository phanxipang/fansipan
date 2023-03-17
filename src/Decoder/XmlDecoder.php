<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Response;

final class XmlDecoder implements DecoderInterface
{
    public function supports(Response $response): bool
    {
        return $response->header('Content-Type') === 'application/xml';
    }

    public function decode(Response $response): array
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
