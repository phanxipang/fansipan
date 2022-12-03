<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Illuminate\Contracts\Pipeline\Pipeline as PipelineInterface;
use Jenky\Atlas\Middleware;
use Jenky\Atlas\Pipeline;

trait HasMiddleware
{
    /**
     * The pipeline instance.
     *
     * @var \Illuminate\Contracts\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * The middleware instance.
     *
     * @var \Jenky\Atlas\Middleware
     */
    protected $middleware;

    /**
     * Set the pipeline instance.
     *
     * @param  \Illuminate\Contracts\Pipeline\Pipeline  $pipeline
     * @return $this
     */
    public function withPipeline(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;

        return $this;
    }

    /**
     * Get the pipeline instance.
     *
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    public function pipeline(): PipelineInterface
    {
        if (is_null($this->pipeline)) {
            $this->pipeline = $this->definePipeline();
        }

        return $this->pipeline;
    }

    /**
     * Define the default pipeline instance.
     *
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    protected function definePipeline(): PipelineInterface
    {
        return new Pipeline();
    }

    /**
     * Get the middleware instance.
     *
     * @return \Jenky\Atlas\Middleware
     */
    public function middleware(): Middleware
    {
        if (is_null($this->middleware)) {
            $this->middleware = new Middleware($this->defaultMiddleware());
        }

        return $this->middleware;
    }

    /**
     * Get default middleware.
     *
     * @return array
     */
    protected function defaultMiddleware(): array
    {
        return [];
    }
}
