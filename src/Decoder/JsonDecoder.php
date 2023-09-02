<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Exception\NotDecodableException;
use Psr\Http\Message\ResponseInterface;

final class JsonDecoder implements DecoderInterface
{
    /**
     * @throws \Jenky\Atlas\Exception\NotDecodableException
     */
    public function decode(ResponseInterface $response): array
    {
        if (! $this->supports($response)) {
            throw NotDecodableException::create();
        }

        return \json_decode((string) $response->getBody(), true) ?? [];
    }

    /**
     * Determine wether decoder is supported for given response.
     */
    private function supports(ResponseInterface $response): bool
    {
        return \mb_strpos($response->getHeaderLine('Content-Type'), 'json') !== false;
    }
}
