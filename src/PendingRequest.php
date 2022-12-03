<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Http\Discovery\Psr17FactoryDiscovery;
use InvalidArgumentException;
use Jenky\Atlas\Contracts\PendingRequestInterface;
use Psr\Http\Message\RequestInterface;

class PendingRequest implements PendingRequestInterface
{
    /**
     * @var \Jenky\Atlas\Request
     */
    protected $request;

    /**
     * @var \Jenky\Atlas\Connector
     */
    protected $connector;

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
     * @return $this
     */
    public static function from(Request $request)
    {
        return new static($request);
    }

    /**
     * Send the request through connector middleware.
     *
     * @return \Jenky\Atlas\Response
     */
    public function send(): Response
    {
        return $this->connector->pipeline()
            ->send($this->request)
            ->through($this->gatherMiddleware())
            ->then(function ($request) {
                return new Response(
                    $this->connector->client()->sendRequest(
                        $this->createRequest()
                    )
                );
            });
    }

    /**
     * Gather all the middleware from the connector instance.
     *
     * @return array
     */
    protected function gatherMiddleware(): array
    {
        $middleware = $this->connector->middleware();

        $middleware->prepend(Middleware\AttachRequestContentTypeHeader::class, 'body_format_content_type');
        $middleware->push(Middleware\CastsResponseToDto::class, 'dto');

        return array_filter(array_map(function ($item) {
            return $item[0] ?? null;
        }, $middleware->all()));
    }

    /**
     * Build the request URI.
     *
     * @return string
     */
    protected function uri(): string
    {
        $uri = $this->request->endpoint();

        if ($this->request->query()->isNotEmpty()) {
            $uri .= '?'.http_build_query($this->request->query()->all());
        }

        return $uri;
    }

    /**
     * Create a connector instance.
     *
     * @return \Jenky\Atlas\Connector
     *
     * @throws \InvalidArgumentException
     */
    protected function createConnector(): Connector
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

    /**
     * Create new PSR request instance.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createRequest(): RequestInterface
    {
        $request = Psr17FactoryDiscovery::findRequestFactory()->createRequest(
            $this->request->method(), $this->uri()
        );

        if ($this->request->headers()->isNotEmpty()) {
            foreach ($this->request->headers() as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }

        return $request->withBody(
            Psr17FactoryDiscovery::findStreamFactory()->createStream(
                (string) $this->request->body()
            )
        );
    }
}
