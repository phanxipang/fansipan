<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Http\Factory\Discovery\HttpClient;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineInterface;
use Psr\Http\Client\ClientInterface;

class Connector
{
    /**
     * The HTTP client instance.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    protected $client;

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
     * Get and set the HTTP client instance.
     *
     * @param  null|\Psr\Http\Client\ClientInterface $client
     * @return \Psr\Http\Client\ClientInterface
     */
    public function client(?ClientInterface $client = null): ClientInterface
    {
        if ($client) {
            $this->client = $client;
        }

        if (is_null($this->client)) {
            $this->client = $this->defineClient();
        }

        return $this->client;
    }

    /**
     * Define the default HTTP client instance.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    protected function defineClient(): ClientInterface
    {
        return HttpClient::client();
    }

    /**
     * Get and set the pipeline instance.
     *
     * @param  null|\Illuminate\Contracts\Pipeline\Pipeline  $pipeline
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    public function pipeline(?PipelineInterface $pipeline = null): PipelineInterface
    {
        if ($pipeline) {
            $this->pipeline = $pipeline;
        }

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

    /**
     * Send the request.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return \Jenky\Atlas\Response
     */
    public function send(Request $request): Response
    {
        return PendingRequest::from(
            $request->withConnector($this)
        )->send();
    }
}
