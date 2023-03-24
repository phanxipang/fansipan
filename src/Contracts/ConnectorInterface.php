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
     * Get the base uri for the HTTP client.
     */
    public function baseUri(): ?string;

    /**
     * Return the instance with provided HTTP client.
     *
     * @return mixed
     */
    public function withClient(ClientInterface $client);

    /**
     * Get the HTTP client instance.
     */
    public function client(): ClientInterface;

    /**
     * Return the instance with provided pipeline.
     *
     * @return mixed
     */
    public function withPipeline(PipelineInterface $pipeline);

    /**
     * Get the pipeline instance.
     */
    public function pipeline(): PipelineInterface;

    /**
     * Get the middleware instance.
     */
    public function middleware(): Middleware;

    /**
     * Send the given request.
     *
     * The request and response should be processed through the middleware via
     * the pipeline.
     */
    public function send(Request $request): Response;
}
