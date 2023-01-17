<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use BadMethodCallException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Jenky\Atlas\Request;
use Jenky\Atlas\RequestCollectionProxy;

trait HasRequestCollection
{
    /**
     * Build the request collection.
     */
    protected function requests(array $collection): array
    {
        $requests = [];

        foreach ($collection as $key => $value) {
            if (is_numeric($key)) {
                $method = Str::camel(class_basename($value));
                $requests[$method] = $value;
            } else {
                $requests[$key] = $value;
            }
        }

        return $requests;
    }

    /**
     * Create new instance of request from the collection mapping.
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $requests = property_exists($this, 'requests') ? (array) $this->requests : [];

        $request = $this->requests($requests)[$method] ?? null;

        if (! $request) {
            throw new BadMethodCallException(sprintf(
                'Call to undefined method %s::%s()', static::class, $method
            ));
        }

        if (is_array($request)) {
            return new RequestCollectionProxy($this, $this->requests($request));
        }

        if (! is_a($request, Request::class, true)) {
            throw new InvalidArgumentException(
                sprintf('%s must be instance of %s', $request, Request::class)
            );
        }

        return $this->request(new $request(...$parameters));
    }
}
