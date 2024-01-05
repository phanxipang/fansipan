<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

use Psr\Http\Message\ResponseInterface;

interface DecoderInterface
{
    /**
     * Decode response body.
     *
     * @throws \Fansipan\Exception\NotDecodableException if decoder is unable to decode the response
     */
    public function decode(ResponseInterface $response): iterable;
}
