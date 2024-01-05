<?php

declare(strict_types=1);

namespace Fansipan\Mapper;

use Fansipan\Contracts\DecoderInterface;
use Fansipan\Contracts\MapperInterface;
use Fansipan\Decoder\ChainDecoder;
use Psr\Http\Message\ResponseInterface;

/**
 * @template T of object
 * @implements MapperInterface<T>
 */
final class GenericMapper implements DecoderInterface, MapperInterface
{
    /**
     * @var callable(iterable): ?T
     */
    private $onSuccess;

    /**
     * @var callable(iterable): ?T
     */
    private $onFailure;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @param  callable(iterable): ?T $onSuccess
     * @param  callable(iterable): ?T $onFailure
     */
    public function __construct(
        callable $onSuccess,
        callable $onFailure,
        ?DecoderInterface $decoder = null
    ) {
        $this->onSuccess = $onSuccess;
        $this->onFailure = $onFailure;
        $this->decoder = $decoder ?? ChainDecoder::default();
    }

    public function map(ResponseInterface $response): ?object
    {
        $status = $response->getStatusCode();
        $decoded = $this->decoder->decode($response);

        if ($status >= 200 && $status < 300) {
            return ($this->onSuccess)($decoded);
        } else {
            return ($this->onFailure)($decoded);
        }
    }

    public function decode(ResponseInterface $response): iterable
    {
        return $this->decoder->decode($response);
    }
}
