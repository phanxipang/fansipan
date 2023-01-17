<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Contracts\PayloadInterface;
use Jenky\Atlas\Map;

class JsonPayload extends Map implements PayloadInterface
{
    /**
     * Get the header content type value.
     */
    public function contentType(): ?string
    {
        return 'application/json';
    }

    /**
     * Get the string representation of the payload.
     */
    public function __toString()
    {
        return json_encode($this->all());
    }
}
