<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Contracts\PayloadInterface;

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
