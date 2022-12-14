<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Middleware;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Psr\Http\Client\ClientInterface;

interface ConnectorInterface
{
    /**
     * Return the instance with provided HTTP client.
     *
     * @param  \Psr\Http\Client\ClientInterface  $client
     * @return mixed
     */
    public function withClient(ClientInterface $client);

    /**
     * Get the HTTP client instance.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    public function client(): ClientInterface;

    /**
     * Return the instance with provided pipeline.
     *
     * @param  Pipeline  $pipeline
     * @return mixed
     */
    public function withPipeline(PipelineInterface $pipeline);

    /**
     * Get the pipeline instance.
     *
     * @return \Jenky\Atlas\Contracts\PipelineInterface
     */
    public function pipeline(): PipelineInterface;

    /**
     * Get the middleware instance.
     *
     * @return \Jenky\Atlas\Middleware
     */
    public function middleware(): Middleware;

    /**
     * Send the given request.
     *
     * The request and response should be processed through the middleware via
     * the pipeline.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return \Jenky\Atlas\Response
     */
    public function send(Request $request): Response;
}
