<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Response;

final class JsonDecoder implements DecoderInterface
{
    public function supports(Response $response): bool
    {
        return mb_strpos($response->header('Content-Type'), 'json') !== false;
    }

    public function decode(Response $response): array
    {
        return json_decode($response->body(), true) ?? [];
    }
}
