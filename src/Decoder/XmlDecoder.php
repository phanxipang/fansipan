<?php

declare(strict_types=1);

namespace Fansipan\Decoder;

use Fansipan\Contracts\DecoderInterface;
use Fansipan\Exception\NotDecodableException;
use Psr\Http\Message\ResponseInterface;

final class XmlDecoder implements DecoderInterface
{
    /**
     * @return  array<array-key, mixed>
     *
     * @throws \Fansipan\Exception\NotDecodableException
     */
    public function decode(ResponseInterface $response): iterable
    {
        if (! $this->supports($response)) {
            throw NotDecodableException::create();
        }

        $xml = \simplexml_load_string((string) $response->getBody());

        if (! $xml) {
            return [];
        }

        return \json_decode(
            \json_encode($xml) ?: '[]', true
        );
    }

    /**
     * Determine wether decoder is supported for given response.
     */
    private function supports(ResponseInterface $response): bool
    {
        return \mb_strpos($response->getHeaderLine('Content-Type'), 'xml') !== false;
    }
}
