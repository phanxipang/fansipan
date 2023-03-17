<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class MockResponse
{
    /**
     * Create a new response instance.
     *
     * @param  string|array|callable $body
     * @param  array<string, mixed> $headers
     *
     * @throws \Http\Discovery\Exception\NotFoundException
     * @throws \InvalidArgumentException
     */
    public static function make($body, int $status = 200, array $headers = []): ResponseInterface
    {
        $response = Psr17FactoryDiscovery::findResponseFactory()
            ->createResponse($status);

        if (is_array($body)) {
            $body = json_encode($body);
            $headers['Content-Type'] = 'application/json';
        }

        if (! is_callable($body)) {
            $body = function (StreamFactoryInterface $factory) use ($body) {
                return $factory->createStream($body);
            };
        }

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response->withBody($body(Psr17FactoryDiscovery::findStreamFactory()));
    }
}
