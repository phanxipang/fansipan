<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Contracts\PayloadInterface;
use LogicException;

class RawPayload implements PayloadInterface
{
    protected $payload = '';

    public function __construct(string $payload = '')
    {
        $this->payload = $payload;
    }

    /**
     * Get the header content type value.
     *
     * @return null|string
     */
    public function contentType(): ?string
    {
        return null;
    }

    public function all(): string
    {
        return $this->payload;
    }

    public function set($value)
    {
        $this->payload = (string) $value;

        return $this;
    }

    public function merge(...$values)
    {
        throw new LogicException('Raw body payload does not support to merge values.');
    }

    public function with(string $key, $value)
    {
        throw new LogicException('Raw body payload does not support to set value by key.');
    }

    public function push($value, ?string $key = null)
    {
        if (! $key) {
            $this->payload .= (string) $value;

            return $this;
        }

        throw new LogicException('Raw body payload does not support to push new value.');
    }

    public function remove(string $key)
    {
        throw new LogicException('Raw body payload does not support to remove a value by key.');
    }

    public function isEmpty(): bool
    {
        return $this->payload === '';
    }

    /**
     * Get the string representation of the payload.
     */
    public function __toString()
    {
        return $this->payload;
    }
}
