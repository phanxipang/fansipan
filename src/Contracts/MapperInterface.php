<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

use Psr\Http\Message\ResponseInterface;

/**
 * @template T of object
 */
interface MapperInterface
{
    /**
     * Map the response body to an object.
     *
     * @return ?T
     */
    public function map(ResponseInterface $response): ?object;
}
