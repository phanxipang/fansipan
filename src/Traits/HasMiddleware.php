<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Middleware;

trait HasMiddleware
{
    /**
     * The middleware instance.
     *
     * @var \Jenky\Atlas\Middleware
     */
    private $middleware;

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

    /**
     * Gather all the middleware.
     */
    private function gatherMiddleware(): array
    {
        return array_filter(array_map(function ($item) {
            return $item[0] ?? null;
        }, $this->middleware()->all()));
    }
}
