<?php

namespace Jenky\Atlas;

use Closure;
use Jenky\Atlas\Contracts\PipelineInterface;
use Throwable;

final class Pipeline implements PipelineInterface
{
    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    private $passable;

    /**
     * The array of class pipes.
     *
     * @var array
     */
    private $pipes = [];

    /**
     * Set the object being sent through the pipeline.
     */
    public function send($passable): self
    {
        $clone = clone $this;

        $clone->passable = $passable;

        return $clone;
    }

    /**
     * Set the array of pipes.
     */
    public function through(iterable $stops): self
    {
        $clone = clone $this;

        $clone->pipes = $stops instanceof \Traversable ? iterator_to_array($stops) : $stops;

        return $clone;
    }

    /**
     * Push additional pipes onto the pipeline.
     *
     * @param  array|mixed  $pipes
     */
    public function pipe($pipes): self
    {
        $clone = clone $this;

        array_push($clone->pipes, ...(is_array($pipes) ? $pipes : func_get_args()));

        return $clone;
    }

    /**
     * Run the pipeline with a final destination callback.
     */
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    /**
     * Get the final piece of the Closure onion.
     */
    private function prepareDestination(Closure $destination): Closure
    {
        return function ($passable) use ($destination) {
            try {
                return $destination($passable);
            } catch (Throwable $e) {
                return $this->handleException($passable, $e);
            }
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     */
    private function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                try {
                    if (is_callable($pipe)) {
                        // If the pipe is a callable, then we will call it directly, but otherwise we
                        // will resolve the pipes out of the dependency container and call it with
                        // the appropriate method and arguments, returning the results back out.
                        return $pipe($passable, $stack);
                    } elseif (! is_object($pipe)) {
                        [$name, $parameters] = $this->parsePipeString($pipe);

                        // If the pipe is a string we will parse the string and resolve the class out
                        // of the dependency injection container. We can then build a callable and
                        // execute the pipe function giving in the parameters that are required.
                        $pipe = new $name();

                        $parameters = array_merge([$passable, $stack], $parameters);
                    } else {
                        // If the pipe is already an object we'll just make a callable and pass it to
                        // the pipe as-is. There is no need to do any extra parsing and formatting
                        // since the object we're given was already a fully instantiated object.
                        $parameters = [$passable, $stack];
                    }

                    /** @phpstan-ignore-next-line */
                    $carry = $pipe(...$parameters);

                    return $this->handleCarry($carry);
                } catch (Throwable $e) {
                    return $this->handleException($passable, $e);
                }
            };
        };
    }

    /**
     * Parse full pipe string to get name and parameters.
     */
    private function parsePipeString(string $pipe): array
    {
        [$name, $parameters] = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Handle the value returned from each pipe before passing it to the next.
     *
     * @param  mixed  $carry
     * @return mixed
     */
    private function handleCarry($carry)
    {
        return $carry;
    }

    /**
     * Handle the given exception.
     *
     * @param  mixed  $passable
     * @return mixed
     *
     * @throws \Throwable
     */
    private function handleException($passable, Throwable $e)
    {
        throw $e;
    }
}
