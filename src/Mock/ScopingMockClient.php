<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ScopingMockClient implements ClientInterface
{
    use AssertTrait;

    /**
     * @var null|iterable|\Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[]
     */
    private $defaultResponse = null;

    /**
     * @var array<string, mixed>
     */
    private $conditionalResponses = [];

    public function __construct(iterable $responses)
    {
        $this->setResponses($responses);
    }

    /**
     * Set the responses.
     */
    public function setResponses(iterable $responses): void
    {
        foreach ($responses as $key => $response) {
            if (! is_string($key)) {
                continue;
            }

            $this->addResponse($key, $response);
        }
    }

    /**
     * Add an response with a condition.
     *
     * @param  iterable|\Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[] $response
     */
    public function addResponse(string $condition, $response): void
    {
        if ($condition === '*') {
            $this->defaultResponse = $response;
        } else {
            $this->conditionalResponses[$condition] = $response;
        }
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        foreach ($this->conditionalResponses as $uri => $responseFactory) {
            if (Uri::matches($uri, (string) $request->getUri())) {
                return $this->sendAndRecord($request, $responseFactory);
            }
        }

        return $this->sendAndRecord($request, $this->defaultResponse);
    }

    /**
     * @param  null|iterable|\Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[] $response
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    private function sendAndRecord(RequestInterface $request, $response): ResponseInterface
    {
        $response = $this->createMockClient($response)
            ->sendRequest($request);

        $this->record($request, $response);

        return $response;
    }

    /**
     * Create a mock client.
     *
     * @param  null|iterable|\Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[] $response
     */
    private function createMockClient($response): MockClient
    {
        return new MockClient($response);
    }
}
