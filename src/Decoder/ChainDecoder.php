<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Exception\NotDecodableException;
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
     * @throws \Jenky\Atlas\Exception\NotDecodableException
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
