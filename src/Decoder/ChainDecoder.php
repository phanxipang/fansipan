<?php

declare(strict_types=1);

namespace Jenky\Atlas\Decoder;

use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Exception\NotDecodableException;
use Psr\Http\Message\ResponseInterface;

final class ChainDecoder implements DecoderInterface
{
    /**
     * @var DecoderInterface[]
     */
    private $decoders;

    public function __construct(DecoderInterface ...$decoders)
    {
        $this->decoders = $decoders;
    }

    public function supports(ResponseInterface $request): bool
    {
        return ! empty($this->decoders);
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
