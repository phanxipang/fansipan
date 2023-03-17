<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Response;

interface DecoderInterface
{
    /**
     * Determine wether decoder is supported for given response.
     */
    public function supports(Response $response): bool;

    /**
     * Decode response body to native array type.
     */
    public function decode(Response $response): array;
}
