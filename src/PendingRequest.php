<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\PipelineInterface;
use Jenky\Atlas\Contracts\RetryableInterface;
use Jenky\Atlas\Exceptions\RetryException;
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
        return $this->connector instanceof RetryableInterface
            ? $this->sendAndRetryRequest($request)
            : $this->sendRequest($request);
    }

    private function sendRequest(Request $request): Response
    {
        $response = $this->pipeline->send(Util::request($request, $this->connector->baseUri()))
            ->through($this->gatherMiddleware())
            ->then(function (RequestInterface $request) {
                return $this->connector->client()->sendRequest($request);
            });

        return new Response($response, $request->decoder());
    }

    private function sendAndRetryRequest(Request $request): Response
    {
        /* beginning:

        try {
            return $this->sendRequest($request);
        } catch (RetryException $e) {
            if (! $e->retryable()) {
                return new Response($e->response(), $request->decoder());
            }

            $delay = $e->delay();

            if ($delay > 0) {
                usleep($delay * 1000);
            }

            goto beginning;
        } */

        do {
            try {
                return $this->sendRequest($request);
            } catch (RetryException $e) {
                if (! $e->retryable()) {
                    // return $e->response();
                    return new Response($e->response(), $request->decoder());
                }

                $delay = $e->delay();

                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
        } while (true);
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
