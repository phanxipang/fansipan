<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Psr\Http\Message\ResponseInterface;

final class XmlDecoder implements DecoderInterface
{
    public function supports(ResponseInterface $response): bool
    {
        return $response->getHeaderLine('Content-Type') === 'application/xml';
    }

    public function decode(ResponseInterface $response): array
    {
        $xml = simplexml_load_string((string) $response->getBody());

        if (! $xml) {
            return [];
        }

        return json_decode(
            json_encode($xml) ?: '[]', true
        );
    }
}
