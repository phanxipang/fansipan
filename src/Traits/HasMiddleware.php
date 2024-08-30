<?php

declare(strict_types=1);

namespace Fansipan\Traits;

use Fansipan\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HasMiddleware
{
    /**
     * The middleware instance.
     *
     * @var Middleware
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
     *
     * @return array<array-key, callable(RequestInterface, callable): ResponseInterface>
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
        return array_filter(array_map(static function (array $item) {
            return $item[0] ?? null;
        }, $this->middleware()->all()));
    }
}
