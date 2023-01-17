<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Contracts\PipelineInterface;
use Jenky\Atlas\Middleware;
use Jenky\Atlas\Pipeline;

trait HasMiddleware
{
    /**
     * The pipeline instance.
     *
     * @var \Illuminate\Contracts\Pipeline\Pipeline
     */
    private $pipeline;

    /**
     * The middleware instance.
     *
     * @var \Jenky\Atlas\Middleware
     */
    private $middleware;

    /**
     * Set the pipeline instance.
     */
    public function withPipeline(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;

        return $this;
    }

    /**
     * Get the pipeline instance.
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
     */
    protected function definePipeline(): PipelineInterface
    {
        return new Pipeline();
    }

    /**
     * Get the middleware instance.
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
     */
    protected function defaultMiddleware(): array
    {
        return [];
    }
}
