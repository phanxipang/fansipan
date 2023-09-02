<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Contracts\PayloadInterface;

trait AsMultipart
{
    /**
     * Create new multipart request body.
     */
    protected function definePayload(): PayloadInterface
    {
        return new MultipartPayload(\is_array($this->defaultBody()) ? $this->defaultBody() : []);
    }
}
