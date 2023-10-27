<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

interface PayloadInterface extends \Stringable
{
    /**
     * Get the header content type value.
     */
    public function contentType(): ?string;

    /**
     * Get the payload data.
     *
     * @return mixed
     */
    public function all();

    /**
     * Set the payload value.
     *
     * @param  string|array  $value
     * @return static
     */
    public function set($value);

    /**
     * Merge the data to the payload.
     *
     * @param  mixed  $values
     * @return static
     */
    public function merge(...$values);

    /**
     * Set value to the payload for given key.
     *
     * @param  mixed  $value
     * @return static
     */
    public function with(string $key, $value);

    /**
     * Push a value onto an payload.
     *
     * @param  mixed  $value
     * @param  null|string  $key
     * @return static
     */
    public function push($value, ?string $key = null);

    /**
     * Delete an item from the payload by its unique key.
     *
     * @return static
     */
    public function remove(string $key);

    /**
     * Determine whether the payload data is empty.
     */
    public function isEmpty(): bool;
}
