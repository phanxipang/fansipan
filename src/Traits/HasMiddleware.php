<?php

declare(strict_types=1);

namespace Fansipan\Traits;

use Fansipan\Middleware;

trait HasMiddleware
{
    /**
     * The middleware instance.
     *
     * @var \Fansipan\Middleware
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
        return array_filter(array_map(function (array $item) {
            return $item[0] ?? null;
        }, $this->middleware()->all()));
    }
}
