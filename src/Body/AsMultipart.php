<?php

declare(strict_types=1);

namespace Fansipan\Body;

use Fansipan\Contracts\PayloadInterface;

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
