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

    public function supports(ResponseInterface $request): bool
    {
        return iterator_count($this->decoders) > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Jenky\Atlas\Exception\NotDecodableException
     */
    public function decode(ResponseInterface $response): array
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->supports($response)) {
                return $decoder->decode($response);
            }
        }

        throw new NotDecodableException('Unable to decode the response body.');
    }
}
