<?php

declare(strict_types=1);

namespace Fansipan\Decoder;

use Fansipan\Contracts\DecoderInterface;
use Fansipan\Exception\NotDecodableException;
use Psr\Http\Message\ResponseInterface;

final class ChainDecoder implements DecoderInterface
{
    /**
     * @var iterable<DecoderInterface>
     */
    private $decoders;

    /**
     * @param  iterable<DecoderInterface> $decoders
     */
    public function __construct(iterable $decoders)
    {
        $this->decoders = $decoders;
    }

    /**
     * @throws \Fansipan\Exception\NotDecodableException
     */
    public function decode(ResponseInterface $response): array
    {
        foreach ($this->decoders as $decoder) {
            try {
                return $decoder->decode($response);
            } catch (NotDecodableException $e) {
                continue;
            }
        }

        throw NotDecodableException::create();
    }
}
