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
     * @return mixed
     */
    public function send($traveler);

    /**
     * Set the stops of the pipeline.
     *
     * @return mixed
     */
    public function through(array $stops);

    /**
     * Push additional pipes onto the pipeline.
     *
     * @param  array|mixed  $pipes
     * @return mixed
     */
    public function pipe($pipes);

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination);
}
