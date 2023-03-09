<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use BadMethodCallException;
use InvalidArgumentException;

class RequestCollectionProxy
{
    /**
     * @var \Jenky\Atlas\Connector
     */
    private $connector;

    /**
     * @var array
     */
    private $collection;

    /**
     * Create new collection proxy instance.
     *
     * @param  \Jenky\Atlas\Connector  $connector
     * @param  array  $collection
     * @return void
     */
    public function __construct(Connector $connector, array $collection)
    {
        $this->connector = $connector;
        $this->collection = $collection;
    }

    /**
     * Create new instance of request from the collection mapping.
     *
     * @param  mixed  $method
     * @param  array  $parameters
     * @return \Jenky\Atlas\Request
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $request = $this->collection[$method] ?? null;

        if (! $request) {
            throw new BadMethodCallException(sprintf(
                'Call to undefined method %s::%s()', static::class, $method
            ));
        }

        if (! is_a($request, Request::class, true)) {
            throw new InvalidArgumentException(
                sprintf('%s must be instance of %s', $request, Request::class)
            );
        }

        return new PendingRequest($this->connector, new $request(...$parameters));
    }
}
