<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Util
{
    /**
     * Create a new PSR URI instance.
     *
     * @throws \Http\Discovery\Exception\NotFoundException
     * @throws \InvalidArgumentException
     */
    public static function uri(string $uri, array $query = []): UriInterface
    {
        $psrUri = Psr17FactoryDiscovery::findUriFactory()
            ->createUri($uri);

        return $query ? $psrUri->withQuery(http_build_query($query)) : $psrUri;
    }

    /**
     * Create a new PSR request instance from given request
     *
     * @throws \Http\Discovery\Exception\NotFoundException
     * @throws \InvalidArgumentException
     */
    public static function request(Request $request): RequestInterface
    {
        $psrRequest = Psr17FactoryDiscovery::findRequestFactory()
            ->createRequest(
                $request->method(),
                static::uri(
                    $request->endpoint(), $request->query()->all()
                )
            );

        if ($request->headers()->isNotEmpty()) {
            foreach ($request->headers() as $name => $value) {
                $psrRequest = $psrRequest->withHeader($name, $value);
            }
        }

        return $psrRequest->withBody(
            Psr17FactoryDiscovery::findStreamFactory()
                ->createStream((string) $request->body())
        );
    }
}
