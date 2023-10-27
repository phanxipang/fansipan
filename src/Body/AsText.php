<?php

declare(strict_types=1);

namespace Fansipan\Body;

use Fansipan\Contracts\PayloadInterface;

trait AsText
{
    /**
     * Create new text request body.
     */
    protected function definePayload(): PayloadInterface
    {
        return new RawPayload($this->defaultBody() ?: '');
    }
}
