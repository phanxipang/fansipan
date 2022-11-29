<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class Middleware implements IteratorAggregate, Countable
{
    protected $middleware = [];

    protected $except = [];

    public function __construct(array $middleware = [])
    {
        foreach ($middleware as $name => $value) {
            $this->push($value, is_string($name) ? $name : '');
        }
    }

    public function all(): array
    {
        if (! empty($this->except)) {
            $middleware = array_filter($this->except, function ($item) {
                return ! in_array($item[1] ?? null, $this->except);
            });

            $this->except = [];

            return $middleware;
        }

        return $this->middleware;
    }

    public function push($middleware, string $name = '')
    {
        $this->middleware[] = [$middleware, $name];

        return $this;
    }

    public function prepend($middleware, string $name = '')
    {
        array_unshift($this->middleware, [$middleware, $name]);

        return $this;
    }

    public function before(string $findName, $middleware, string $name = '')
    {
        return $this->splice($findName, $name, $middleware, true);
    }

    public function after(string $findName, $middleware, string $name = '')
    {
        return $this->splice($findName, $name, $middleware, false);
    }

    public function without($name)
    {
        $this->except = is_array($name) ? $name : func_get_args();

        return $this;
    }

    public function remove($remove)
    {
        $idx = is_callable($remove) ? 0 : 1;
        $this->middleware = array_values(array_filter(
            $this->middleware,
            static function ($tuple) use ($idx, $remove) {
                return $tuple[$idx] !== $remove;
            }
        ));

        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->middleware);
    }

    public function count(): int
    {
        return count($this->middleware);
    }

    public function isEmpty(): bool
    {
        return ! $this->isNotEmpty();
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    private function findByName(string $name): int
    {
        foreach ($this->middleware as $k => $v) {
            if ($v[1] === $name) {
                return $k;
            }
        }

        throw new InvalidArgumentException("Middleware not found: $name");
    }

    private function splice(string $findName, string $name, $middleware, bool $before)
    {
        $idx = $this->findByName($findName);
        $tuple = [$middleware, $name];

        if ($before) {
            if ($idx === 0) {
                array_unshift($this->middleware, $tuple);
            } else {
                $replacement = [$tuple, $this->middleware[$idx]];
                array_splice($this->middleware, $idx, 1, $replacement);
            }
        } elseif ($idx === count($this->middleware) - 1) {
            $this->middleware[] = $tuple;
        } else {
            $replacement = [$this->middleware[$idx], $tuple];
            array_splice($this->middleware, $idx, 1, $replacement);
        }

        return $this;
    }
}
