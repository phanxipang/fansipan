<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Contracts\PipelineInterface;
use Jenky\Atlas\Pipeline;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Jenky\Atlas\Util;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait ConnectorTrait
{
    use HasClient;
    use HasMiddleware;

    /**
     * Get the base uri for the HTTP client.
     */
    public static function baseUri(): ?string
    {
        return null;
    }

    /**
     * Send the given request.
     *
     * The request and response should be processed through middleware.
     */
    public function send(Request $request): Response
    {
        $response = $this->sendRequest(
            Util::request($request, static::baseUri())
        );

        return new Response($response, $request->decoder());
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->pipeline()
            ->send($request)
            ->through($this->gatherMiddleware())
            ->then(function (RequestInterface $request) {
                return $this->client()->sendRequest($request);
            });
    }

    /**
     * Get the pipeline instance.
     */
    protected function pipeline(): PipelineInterface
    {
        return new Pipeline();
    }
}
