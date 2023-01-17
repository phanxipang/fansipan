<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

interface PayloadInterface
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
     * @return mixed
     */
    public function set($value);

    /**
     * Merge the data to the payload.
     *
     * @param  mixed  $values
     * @return mixed
     */
    public function merge(...$values);

    /**
     * Set value to the payload for given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function with(string $key, $value);

    /**
     * Push a value onto an payload.
     *
     * @param  mixed  $value
     * @param  null|string  $key
     * @return mixed
     */
    public function push($value, ?string $key = null);

    /**
     * Delete an item from the payload by its unique key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function remove(string $key);

    /**
     * Determine whether the payload data is empty.
     */
    public function isEmpty(): bool;
}
