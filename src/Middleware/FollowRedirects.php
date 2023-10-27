<?php

declare(strict_types=1);

namespace Fansipan\Middleware;

use Fansipan\Exception\TooManyRedirectsException;
use Fansipan\Util;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class FollowRedirects
{
    /**
     * @var int
     */
    private $max = 5;

    /**
     * @var array
     */
    private $protocols = [];

    /**
     * @var bool
     */
    private $strict = false;

    /**
     * @var bool
     */
    private $referer = false;

    /**
     * @var int
     */
    private $redirects = 0;

    public function __construct(
        int $max = 5,
        array $protocols = ['http', 'https'],
        bool $strict = false,
        bool $referer = false
    ) {
        if ($max < 1) {
            throw new \InvalidArgumentException('Invalid redirect limit: '.$max);
        }

        $this->max = $max;
        $this->protocols = $protocols;
        $this->strict = $strict;
        $this->referer = $referer;
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $response = $next($request);

        if (\strpos((string) $response->getStatusCode(), '3') !== 0 ||
            ! $response->hasHeader('Location')
        ) {
            return $response;
        }

        $this->guardMax($request, $response);

        return $this($this->modifyRequest($request, $response), $next);
    }

    private function guardMax(RequestInterface $request, ResponseInterface $response): void
    {
        ++$this->redirects;

        if ($this->redirects > $this->max) {
            throw new TooManyRedirectsException("Will not follow more than {$this->max} redirects", $request, $response);
        }
    }

    private function modifyRequest(RequestInterface $request, ResponseInterface $response): RequestInterface
    {
        $redirectRequest = clone $request;
        $statusCode = $response->getStatusCode();

        if ($statusCode == 303 ||
            ($statusCode <= 302 && ! $this->strict)
        ) {
            $safeMethods = ['GET', 'HEAD', 'OPTIONS'];
            $requestMethod = $request->getMethod();

            $method = \in_array($requestMethod, $safeMethods) ? $requestMethod : 'GET';

            $redirectRequest = $redirectRequest->withMethod($method);
        }

        $uri = $this->redirectUri($request, $response);

        if ($this->referer &&
            $uri->getScheme() === $request->getUri()->getScheme()
        ) {
            $redirectRequest = $redirectRequest->withHeader('Referer', (string) $request->getUri()->withUserInfo('')->withFragment(''));
        }

        if ($this->isCrossOrigin($request->getUri(), $uri)) {
            $redirectRequest = $redirectRequest
                ->withoutHeader('Authorization')
                ->withoutHeader('Cookie');
        }

        return $redirectRequest->withUri($uri);
    }

    private function redirectUri(RequestInterface $request, ResponseInterface $response): UriInterface
    {
        $location = Util::absoluteUri(
            $request->getUri(),
            Util::uri($response->getHeaderLine('Location'))
        );

        if (! \in_array($location->getScheme(), $this->protocols)) {
            throw new \RuntimeException(sprintf('Redirect URI, %s, does not use one of the allowed redirect protocols: %s', $location, implode(', ', $this->protocols)));
        }

        return $location;
    }

    private function isCrossOrigin(UriInterface $original, UriInterface $modified): bool
    {
        if (strcasecmp($original->getHost(), $modified->getHost()) !== 0) {
            return true;
        }

        if ($original->getScheme() !== $modified->getScheme()) {
            return true;
        }

        if ($this->getPort($original) !== $this->getPort($modified)) {
            return true;
        }

        return false;
    }

    private function getPort(UriInterface $uri): int
    {
        if ($port = $uri->getPort()) {
            return $port;
        }

        return $uri->getScheme() === 'https' ? 443 : 80;
    }
}
