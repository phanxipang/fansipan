<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Psr\Http\Message\ResponseInterface;

interface DecoderInterface
{
    /**
     * Determine wether decoder is supported for given response.
     */
    public function supports(ResponseInterface $response): bool;

    /**
     * Decode response body to native array type.
     */
    public function decode(ResponseInterface $response): array;
}
