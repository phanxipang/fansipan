<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Closure;

interface PipelineInterface
{
    /**
     * Set the traveler object being sent on the pipeline.
     *
     * @param  mixed  $traveler
     */
    public function send($traveler): self;

    /**
     * Set the stops of the pipeline.
     */
    public function through(iterable $stops): self;

    /**
     * Push additional pipes onto the pipeline.
     *
     * @param  array|mixed  $pipes
     */
    public function pipe($pipes): self;

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination);
}
