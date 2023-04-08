<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Exceptions\RetryException;
use Psr\Http\Message\ResponseInterface;

final class PendingRequest
{
    /**
     * @var \Jenky\Atlas\Contracts\ConnectorInterface
     */
    private $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * Send the request through connector middleware.
     */
    public function send(Request $request): Response
    {
        return $this->connector->pipeline()
            ->send($request)
            ->through($this->gatherMiddleware())
            ->then(function ($request) {
                return $this->toResponse(
                    $this->connector->client()->sendRequest(
                        Util::request($request, $this->connector->baseUri())
                    )
                );
            });
    }

    public function sendAndRetry(Request $request): Response
    {
        /* beginning:

        try {
            return $this->send($request);
        } catch (RetryException $e) {
            if (! $e->retryable()) {
                return $e->response();
            }

            $delay = $e->delay();

            if ($delay > 0) {
                usleep($delay * 1000);
            }

            goto beginning;
        } */

        do {
            try {
                return $this->send($request);
            } catch (RetryException $e) {
                if (! $e->retryable()) {
                    return $e->response();
                }

                $delay = $e->delay();

                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
        } while (true);
    }

    /**
     * Decorates the PRS response.
     */
    private function toResponse(ResponseInterface $response): Response
    {
        return new Response($response);
    }

    /**
     * Gather all the middleware from the connector instance.
     */
    private function gatherMiddleware(): array
    {
        $middleware = $this->connector->middleware();

        $middleware->prepend(new Middleware\AttachContentTypeRequestHeader(), 'body_format_content_type');
        $middleware->after('body_format_content_type', new Middleware\SetResponseDecoder(), 'response_decoder');

        return array_filter(array_map(function ($item) {
            return $item[0] ?? null;
        }, $middleware->all()));
    }
}
