<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Response;

interface DecoderAwareInterface
{
    /**
     * Cast the response body to native array.
     */
    public function decode(Response $response): array;
}
