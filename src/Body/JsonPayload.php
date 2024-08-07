<?php

declare(strict_types=1);

namespace Fansipan\Body;

use Fansipan\Contracts\PayloadInterface;
use Fansipan\Map;

final class JsonPayload extends Map implements PayloadInterface
{
    /**
     * @var int
     */
    private $flags;

    public function __construct(array $parameters = [], int $flags = 0)
    {
        parent::__construct($parameters);

        $this->flags = $flags;
    }

    public function contentType(): ?string
    {
        return 'application/json';
    }

    public function __toString()
    {
        return \json_encode($this->all(), $this->flags) ?: '';
    }
}
