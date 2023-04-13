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
}
