<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Response;

interface DtoSerializable
{
    /**
     * Cast the response to DTO.
     *
     * @param  \Jenky\Atlas\Response  $response
     * @return object
     */
    public function toDto(Response $response): object;
}
