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
     * @var \Jenky\Atlas\Contracts\PipelineInterface
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
     *
     * @return static
     */
    public function withPipeline(PipelineInterface $pipeline)
    {
        $clone = clone $this;

        $clone->pipeline = $pipeline;

        return $clone;
    }

    /**
     * Get the pipeline instance.
     */
    public function pipeline(): PipelineInterface
    {
        if (! $this->pipeline instanceof PipelineInterface) {
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
        if (! $this->middleware instanceof Middleware) {
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
