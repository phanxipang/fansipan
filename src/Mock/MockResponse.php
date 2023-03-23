<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class MockResponse
{
    /**
     * Create a new response instance.
     *
     * @param  \Psr\Http\Message\StreamInterface|string|array|callable $body
     * @param  array<string, mixed> $headers
     *
     * @throws \Http\Discovery\Exception\NotFoundException
     * @throws \InvalidArgumentException
     */
    public static function create($body, int $status = 200, array $headers = []): ResponseInterface
    {
        $response = Psr17FactoryDiscovery::findResponseFactory()
            ->createResponse($status);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        if ($body instanceof StreamInterface) {
            return $response->withBody($body);
        }

        if (is_array($body)) {
            $body = json_encode($body) ?: '';

            if (! $response->hasHeader('Content-Type')) {
                $response = $response->withHeader('Content-Type', 'application/json');
            }
        }

        if (! is_callable($body)) {
            $body = function (StreamFactoryInterface $factory) use ($body) {
                return is_resource($body)
                    ? $factory->createStreamFromResource($body)
                    : $factory->createStream($body);
            };
        }

        return $response->withBody($body(Psr17FactoryDiscovery::findStreamFactory()));
    }

    /**
     * Create new response from a file.
     *
     * @throws \Http\Discovery\Exception\NotFoundException
     * @throws \InvalidArgumentException
     */
    public static function fixture(string $filename, int $status = 200, array $headers = []): ResponseInterface
    {
        return static::create(function (StreamFactoryInterface $factory) use ($filename) {
            return $factory->createStreamFromFile($filename);
        }, $status, $headers);
    }
}
