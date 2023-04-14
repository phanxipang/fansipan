<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Traversable;

final class Middleware implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    private $middleware = [];

    public function __construct(array $middleware = [])
    {
        foreach ($middleware as $name => $value) {
            $this->push($value, is_string($name) ? $name : '');
        }
    }

    public function all(): array
    {
        return $this->middleware;
    }

    /**
     * Unshift a middleware to the bottom of the stack.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface $middleware
     */
    public function unshift(callable $middleware, string $name = ''): self
    {
        array_unshift($this->middleware, [$middleware, $name]);

        return $this;
    }

    /**
     * Push a middleware to stack.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface $middleware
     */
    public function push(callable $middleware, string $name = ''): self
    {
        $this->middleware[] = [$middleware, $name];

        return $this;
    }

    /**
     * Prepend a middleware to the top of the stack.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface $middleware
     */
    public function prepend(callable $middleware, string $name = ''): self
    {
        array_unshift($this->middleware, [$middleware, $name]);

        return $this;
    }

    /**
     * Add a middleware before another middleware by name.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface $middleware
     */
    public function before(string $findName, callable $middleware, string $name = ''): self
    {
        $this->splice($findName, $name, $middleware, true);

        return $this;
    }

    /**
     * Add a middleware after another middleware by name.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface $middleware
     */
    public function after(string $findName, callable $middleware, string $name = ''): self
    {
        $this->splice($findName, $name, $middleware, false);

        return $this;
    }

    /**
     * Remove a middleware by instance or name from the stack.
     *
     * @param  callable(RequestInterface, callable): ResponseInterface|string  $remove
     */
    public function remove($remove): self
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

    private function findByName(string $name): int
    {
        foreach ($this->middleware as $k => $v) {
            if ($v[1] === $name) {
                return $k;
            }
        }

        throw new InvalidArgumentException("Middleware not found: $name");
    }

    /**
     * Splices a function into the middleware list at a specific position.
     *
     * @param  callable|string $middleware
     */
    private function splice(string $findName, string $name, $middleware, bool $before): void
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
    }
}
