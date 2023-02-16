<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use InvalidArgumentException;
use Jenky\Atlas\Contracts\ConnectorInterface;
use Psr\Http\Message\ResponseInterface;

final class PendingRequest
{
    /**
     * @var \Jenky\Atlas\Request
     */
    private $request;

    /**
     * @var \Jenky\Atlas\Connector
     */
    private $connector;

    /**
     * Create new pending request instance.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->connector = $this->createConnector();
    }

    /**
     * Create new pending request instance.
     *
     * @param  \Jenky\Atlas\Request  $request
     * @return static
     */
    public static function from(Request $request)
    {
        return new static($request);
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
    protected function toResponse(ResponseInterface $response): Response
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
        $middleware->push(Middleware\CastsResponseToDto::class, 'dto');

        return array_filter(array_map(function ($item) {
            return $item[0] ?? null;
        }, $middleware->all()));
    }

    /**
     * Create a connector instance.
     *
     * @throws \InvalidArgumentException
     */
    protected function createConnector(): ConnectorInterface
    {
        $connector = $this->request->connector();

        if (is_null($connector)) {
            return new Connector();
        }

        if (! is_subclass_of($connector, Connector::class, true)) {
            throw new InvalidArgumentException(
                sprintf('The connector must be a sub class of %s', Connector::class)
            );
        }

        return is_string($connector) ? new $connector() : $connector;
    }
}
