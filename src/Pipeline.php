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

    public function send($passable): self
    {
        $clone = clone $this;

        $clone->passable = $passable;

        return $clone;
    }

    public function through(iterable $pipes): self
    {
        $clone = clone $this;

        $clone->pipes = $pipes instanceof \Traversable ? iterator_to_array($pipes) : $pipes;

        return $clone;
    }

    public function pipe(callable ...$pipes): self
    {
        $clone = clone $this;

        array_push($clone->pipes, ...$pipes);

        return $clone;
    }

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
                    return $pipe($passable, $stack);
                } catch (Throwable $e) {
                    return $this->handleException($passable, $e);
                }
            };
        };
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
