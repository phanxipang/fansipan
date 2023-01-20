<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use BadMethodCallException;
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
                $method = str_replace(' ', '', preg_replace_callback('/\b.(?![A-Z]{2,})/u', static function ($m) use (&$i) {
                    return 1 === ++$i ? ('İ' === $m[0] ? 'i̇' : mb_strtolower($m[0], 'UTF-8')) : mb_convert_case($m[0], \MB_CASE_TITLE, 'UTF-8');
                }, preg_replace('/[^\pL0-9]++/u', ' ', basename(str_replace('\\', '/', $value)))));
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
