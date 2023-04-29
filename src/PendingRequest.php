<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\PipelineInterface;
use Psr\Http\Message\RequestInterface;

final class PendingRequest
{
    /**
     * @var \Jenky\Atlas\Contracts\ConnectorInterface
     */
    private $connector;

    /**
     * @var \Jenky\Atlas\Contracts\PipelineInterface
     */
    private $pipeline;

    public function __construct(ConnectorInterface $connector, ?PipelineInterface $pipeline = null)
    {
        $this->connector = $connector;
        $this->pipeline = $pipeline ?? new Pipeline();
    }

    /**
     * Send the request through connector middleware.
     */
    public function send(Request $request): Response
    {
        $response = $this->pipeline->send(Util::request($request, $this->connector->baseUri()))
            ->through($this->gatherMiddleware())
            ->then(function (RequestInterface $request) {
                return $this->connector->client()->sendRequest($request);
            });

        return new Response($response, $request->decoder());
    }

    /**
     * Gather all the middleware from the connector instance.
     */
    private function gatherMiddleware(): array
    {
        return array_filter(array_map(function ($item) {
            return $item[0] ?? null;
        }, $this->connector->middleware()->all()));
    }
}
