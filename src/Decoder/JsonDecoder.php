<?php

declare(strict_types=1);

namespace Fansipan\Decoder;

use Fansipan\Contracts\DecoderInterface;
use Fansipan\Exception\NotDecodableException;
use Psr\Http\Message\ResponseInterface;

final class JsonDecoder implements DecoderInterface
{
    /**
     * @throws \Fansipan\Exception\NotDecodableException
     */
    public function decode(ResponseInterface $response): iterable
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
