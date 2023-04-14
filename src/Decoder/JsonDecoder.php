<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Psr\Http\Message\ResponseInterface;

final class JsonDecoder implements DecoderInterface
{
    public function supports(ResponseInterface $response): bool
    {
        return mb_strpos($response->getHeaderLine('Content-Type'), 'json') !== false;
    }

    public function decode(ResponseInterface $response): array
    {
        return json_decode((string) $response->getBody(), true) ?? [];
    }
}
