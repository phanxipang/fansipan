<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Contracts\PayloadInterface;

trait AsJson
{
    /**
     * Create new JSON request body.
     */
    protected function definePayload(): PayloadInterface
    {
        return new JsonPayload(\is_array($this->defaultBody()) ? $this->defaultBody() : []);
    }
}
