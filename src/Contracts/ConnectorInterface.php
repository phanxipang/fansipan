<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Illuminate\Contracts\Pipeline\Pipeline as PipelineInterface;
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
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    public function pipeline(): PipelineInterface;

    /**
     * Send the given request.
     *
     * The request should be processed through the middleware via the pipeline
     * before sending by the HTTP client.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return \Jenky\Atlas\Response
     */
    public function send(Request $request): Response;
}
