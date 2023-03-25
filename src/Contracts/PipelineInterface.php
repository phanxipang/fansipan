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
     * @return static
     */
    public function send($traveler);

    /**
     * Set the stops of the pipeline.
     *
     * @return static
     */
    public function through(iterable $pipes);

    /**
     * Push additional pipes onto the pipeline.
     *
     * @return static
     */
    public function pipe(callable ...$pipes);

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination);
}
