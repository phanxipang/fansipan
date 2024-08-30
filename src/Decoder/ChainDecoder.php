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

    public function decode(ResponseInterface $response): iterable
    {
        foreach ($this->decoders as $decoder) {
            if (! $decoder instanceof DecoderInterface) {
                throw new \InvalidArgumentException(sprintf('Decoder must implement %s. %s given', DecoderInterface::class, \get_debug_type($decoder)));
            }

            try {
                yield from $decoder->decode($response);
            } catch (NotDecodableException $e) {
                continue;
            }
        }
    }

    /**
     * Create default chain decoder.
     */
    public static function default(): self
    {
        $decoders = static function () {
            yield from [
                new JsonDecoder(),
                new XmlDecoder(),
            ];
        };

        return new self($decoders());
    }
}
