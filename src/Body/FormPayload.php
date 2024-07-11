<?php

declare(strict_types=1);

namespace Fansipan\Body;

use Fansipan\Contracts\PayloadInterface;
use Fansipan\Map;

final class FormPayload extends Map implements PayloadInterface
{
    public function contentType(): ?string
    {
        return 'application/x-www-form-urlencoded';
    }

    public function __toString()
    {
        return \http_build_query($this->all());
    }
}
