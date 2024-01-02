<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

use Psr\Http\Message\ResponseInterface;

interface MapperInterface extends DecoderInterface
{
    /**
     * Map the response to an object.
     */
    public function map(ResponseInterface $response): ?object;
}
