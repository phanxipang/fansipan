<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use Jenky\Atlas\Util;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ScopingMockClient implements ClientInterface
{
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
        foreach ($this->conditionalResponses as $uri => $response) {
            if ($this->matchesUri($uri, (string) $request->getUri())) {
                return $this->createMockClient($response)
                    ->sendRequest($request);
            }
        }

        return $this->createMockClient($this->defaultResponse)
            ->sendRequest($request);
    }

    /**
     * Match the given uri pattern.
     */
    private function matchesUri(string $pattern, string $value): bool
    {
        $quoted = preg_quote('*', '/');

        $prepare = '*'.preg_replace('/^(?:'.$quoted.')+/u', '', $pattern);

        return Util::stringIs($prepare, $value);
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
