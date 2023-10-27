<?php

declare(strict_types=1);

namespace Fansipan\Body;

use Fansipan\Contracts\PayloadInterface;
use Fansipan\Map;

final class FormPayload extends Map implements PayloadInterface
{
    /**
     * Get the header content type value.
     */
    public function contentType(): ?string
    {
        return 'application/x-www-form-urlencoded';
    }

    /**
     * Get the string representation of the payload.
     */
    public function __toString()
    {
        return \http_build_query($this->all());
    }
}
