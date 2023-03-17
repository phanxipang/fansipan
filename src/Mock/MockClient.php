<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MockClient implements ClientInterface
{
    private $defaultResponse;

    /**
     * @var \Iterator
     */
    private $responses;

    /**
     * @param  null|iterable|\Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[] $responses
     */
    public function __construct($responseFactory = null)
    {
        $this->setResponseFactory($responseFactory);
    }

    private function isSequential(): bool
    {
        return ! $this->defaultResponse instanceof ResponseInterface;
    }

    public function setResponseFactory($responseFactory = null): void
    {
        if (is_null($responseFactory)) {
            $responseFactory = Psr17FactoryDiscovery::findResponseFactory()->createResponse();
        }

        if ($responseFactory instanceof ResponseInterface) {
            $this->defaultResponse = $responseFactory;
            return;
        }

        if (is_array($responseFactory)) {
            $responseFactory = new \ArrayIterator($responseFactory);
        }

        if (! $responseFactory instanceof \Iterator) {
            throw new \InvalidArgumentException('...');
        }

        $this->responses = $responseFactory;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if (! $this->isSequential()) {
            return $this->defaultResponse;
        }

        $method = $request->getMethod();
        $uri = $request->getUri();

        if (! $this->responses->valid()) {
            throw new \InvalidArgumentException('The response factory iterator passed to Mock Client is empty.');
        }

        $responseFactory = $this->responses->current();
        $response = is_callable($responseFactory) ? $responseFactory($method, $uri) : $responseFactory;
        $this->responses->next();

        if (! $response instanceof ResponseInterface) {
            throw new \InvalidArgumentException(sprintf('The response factory passed to Mock Client must return/yield an instance of Psr\Http\Message\ResponseInterface, "%s" given.', get_debug_type($response)));
        }

        return $response;
    }
}
