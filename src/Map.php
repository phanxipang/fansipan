<?php

declare(strict_types=1);

namespace Jenky\Atlas;

class Map implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    protected $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Get the parameters.
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * Determine whether parameter exists by given key name.
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
    }

    /**
     * Set the parameters.
     *
     * @param  array  $value
     * @return $this
     *
     * @throws \UnexpectedValueException
     */
    public function set($value)
    {
        $this->assertArray($value);

        $this->parameters = $value;

        return $this;
    }

    /**
     * Set the value for given key.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function with(string $key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * Push the value to parameters.
     *
     * @param  mixed  $value
     * @return $this
     *
     * @throws \UnexpectedValueException
     */
    public function push($value, ?string $key = null)
    {
        if (! $key) {
            $this->parameters[] = $value;

            return $this;
        }

        $array = $this->parameters[$key] ?? [];

        $this->assertArray($array);

        $array[] = $value;

        return $this->with($key, $array);
    }

    /**
     * Remove value from parameters by given key.
     *
     * @return $this
     */
    public function remove(string $key)
    {
        unset($this->parameters[$key]);

        return $this;
    }

    /**
     * Merge the given values to the parameters.
     *
     * @param  array  ...$values
     * @return $this
     */
    public function merge(...$values)
    {
        $this->parameters = array_replace_recursive(
            array_merge_recursive(
                $this->parameters, ...$values
            )
        );

        return $this;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->parameters);
    }

    public function count(): int
    {
        return \count($this->parameters);
    }

    /**
     * Determine whether the parameters is empty.
     */
    public function isEmpty(): bool
    {
        return ! $this->isNotEmpty();
    }

    /**
     * Determine whether the parameters is not empty.
     */
    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Assert the value is array.
     *
     * @param  mixed  $value
     *
     * @throws \UnexpectedValueException
     */
    protected function assertArray($value): void
    {
        if (! \is_array($value)) {
            throw new \UnexpectedValueException('The value must be an array.');
        }
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->parameters[$offset] ?? null;
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->with($offset, $value);
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
}
