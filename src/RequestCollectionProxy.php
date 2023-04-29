<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use BadMethodCallException;
use InvalidArgumentException;
use Jenky\Atlas\Contracts\ConnectorInterface;

class RequestCollectionProxy
{
    /**
     * @var \Jenky\Atlas\Contracts\ConnectorInterface
     */
    private $connector;

    /**
     * @var array
     */
    private $collection;

    /**
     * Create new collection proxy instance.
     *
     * @param  \Jenky\Atlas\Contracts\ConnectorInterface  $connector
     * @param  array  $collection
     * @return void
     */
    public function __construct(ConnectorInterface $connector, array $collection)
    {
        $this->connector = $connector;
        $this->collection = $collection;
    }

    /**
     * Create new instance of request from the collection mapping.
     *
     * @param  mixed  $method
     * @param  array  $parameters
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters): Response
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

        return $this->connector->send(new $request(...$parameters));
    }
}
