<?php

declare(strict_types=1);

namespace Fansipan;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class Util
{
    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    public static function removeDotSegments(string $path): string
    {
        if ($path === '' || $path === '/') {
            return $path;
        }

        $results = [];
        $segments = \explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                \array_pop($results);
            } elseif ($segment !== '.') {
                $results[] = $segment;
            }
        }

        $newPath = \implode('/', $results);

        if ($path[0] === '/' && (! isset($newPath[0]) || $newPath[0] !== '/')) {
            // Re-add the leading slash if necessary for cases like "/.."
            $newPath = '/'.$newPath;
        } elseif ($newPath !== '' && ($segment === '.' || $segment === '..')) {
            // Add the trailing slash if necessary
            // If newPath is not empty, then $segment must be set and is the last segment from the foreach
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * Converts the relative URI into a new URI that is resolved against the base URI.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-5.2
     */
    public static function absoluteUri(UriInterface $base, UriInterface $relative): UriInterface
    {
        if ((string) $relative === '') {
            return $base;
        }

        if ($relative->getScheme() != '') {
            return $relative->withPath(self::removeDotSegments($relative->getPath()));
        }

        if ($relative->getAuthority() != '') {
            $targetAuthority = $relative->getAuthority();
            $targetPath = self::removeDotSegments($relative->getPath());
            $targetQuery = $relative->getQuery();
        } else {
            $targetAuthority = $base->getAuthority();
            if ($relative->getPath() === '') {
                $targetPath = $base->getPath();
                $targetQuery = $relative->getQuery() != '' ? $relative->getQuery() : $base->getQuery();
            } else {
                if ($relative->getPath()[0] === '/') {
                    $targetPath = $relative->getPath();
                } else {
                    if ($targetAuthority != '' && $base->getPath() === '') {
                        $targetPath = '/'.$relative->getPath();
                    } else {
                        $lastSlashPos = \strrpos($base->getPath(), '/');
                        if ($lastSlashPos === false) {
                            $targetPath = $relative->getPath();
                        } else {
                            $targetPath = \substr($base->getPath(), 0, $lastSlashPos + 1).$relative->getPath();
                        }
                    }
                }
                $targetPath = self::removeDotSegments($targetPath);
                $targetQuery = $relative->getQuery();
            }
        }

        $uri = '';
        $scheme = $base->getScheme();

        if ($scheme != '') {
            $uri .= $scheme.':';
        }

        if ($targetAuthority != '' || $scheme === 'file') {
            $uri .= '//'.$targetAuthority;
        }

        if ($targetAuthority != '' && $targetPath != '' && $targetPath[0] != '/') {
            $targetPath = '/'.$targetPath;
        }

        $uri .= $targetPath;

        if ($targetQuery != '') {
            $uri .= '?'.$targetQuery;
        }

        if ($relative->getFragment() != '') {
            $uri .= '#'.$relative->getFragment();
        }

        return self::uri($uri);
    }

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
    public static function request(Request $request, ?string $baseUri = null): RequestInterface
    {
        $uri = self::uri(
            $request->endpoint(), $request->query()->all()
        );

        $psrRequest = Psr17FactoryDiscovery::findRequestFactory()
            ->createRequest(
                $request->method(),
                $baseUri ? self::absoluteUri(self::uri($baseUri), $uri) : $uri
            );

        if (! $request->headers()->has('Content-Type') &&
            ($contentType = $request->body()->contentType())) {
            $request->headers()->with('Content-Type', $contentType);
        }

        if ($request->headers()->isNotEmpty()) {
            foreach ($request->headers() as $name => $value) {
                $psrRequest = $psrRequest->withHeader($name, $value);
            }
        }

        return $psrRequest->withProtocolVersion($request->version())->withBody(
            Psr17FactoryDiscovery::findStreamFactory()
                ->createStream((string) $request->body())
        );
    }
}
