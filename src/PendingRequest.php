<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Psr\Http\Message\ResponseInterface;

final class PendingRequest
{
    /**
     * @var \Jenky\Atlas\Contracts\ConnectorInterface
     */
    private $connector;

    /**
     * @var \Jenky\Atlas\Request
     */
    private $request;

    /**
     * Create new pending request instance.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(ConnectorInterface $connector, Request $request)
    {
        $this->connector = $connector;
        $this->request = $request;
    }

    /**
     * Send the request through connector middleware.
     */
    public function send(): Response
    {
        return $this->connector->pipeline()
            ->send($this->request)
            ->through($this->gatherMiddleware())
            ->then(function ($request) {
                return $this->toResponse(
                    $this->connector->client()->sendRequest(
                        Util::request($request)
                    )
                );
            });

        /* return $this->toResponse($this->connector->pipeline()
            ->send(Util::request($this->request))
            ->through($this->gatherMiddleware())
            ->then(function ($request) {
                return $this->connector->client()
                    ->sendRequest($request);
            })
        ); */
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
    protected function gatherMiddleware(): array
    {
        $middleware = $this->connector->middleware();

        $middleware->prepend(Middleware\AttachContentTypeRequestHeader::class, 'body_format_content_type');
        $middleware->after('body_format_content_type', Middleware\SetResponseDecoder::class, 'response_decoder');

        return array_filter(array_map(function ($item) {
            return $item[0] ?? null;
        }, $middleware->all()));
    }
}
