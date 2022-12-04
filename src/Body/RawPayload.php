<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Jenky\Atlas\Contracts\PayloadInterface;
use LogicException;

class RawPayload implements PayloadInterface
{
    /**
     * @var string
     */
    protected $payload = '';

    /**
     * Create new raw payload instance.
     *
     * @param  string  $payload
     * @return void
     */
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

    /**
     * Get the payload data.
     *
     * @return string
     */
    public function all(): string
    {
        return $this->payload;
    }

    /**
     * Set the payload value.
     *
     * @param  string  $value
     * @return $this
     */
    public function set($value)
    {
        $this->payload = (string) $value;

        return $this;
    }

    /**
     * Merge the payload data.
     *
     * @param  array  $values
     * @return mixed
     *
     * @throws \LogicException
     */
    public function merge(...$values)
    {
        throw new LogicException('Raw body payload does not support to merge values.');
    }

    /**
     * Set the payload value by given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @throws \LogicException
     */
    public function with(string $key, $value)
    {
        throw new LogicException('Raw body payload does not support to set value by key.');
    }

    /**
     * Push the value to the payload.
     *
     * @param  string  $value
     * @param  null|string  $key
     * @return $this
     *
     * @throws \LogicException
     */
    public function push($value, ?string $key = null)
    {
        if (! $key) {
            $this->payload .= (string) $value;

            return $this;
        }

        throw new LogicException('Raw body payload does not support to push new value.');
    }

    /**
     * Remove the payload data by given key.
     *
     * @param  string  $key
     *
     * @throws \LogicException
     */
    public function remove(string $key)
    {
        throw new LogicException('Raw body payload does not support to remove a value by key.');
    }

    /**
     * Determine whether payload data is empty.
     *
     * @return bool
     */
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
