<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class Util
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
     * Create a new PSR request instance from given request.
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

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|iterable<string> $pattern
     * @param string $value
     * @return bool
     */
    public static function stringIs($pattern, string $value): bool
    {
        if (! is_iterable($pattern)) {
            $pattern = [$pattern];
        }

        foreach ($pattern as $pattern) {
            $pattern = (string) $pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^'.$pattern.'\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
