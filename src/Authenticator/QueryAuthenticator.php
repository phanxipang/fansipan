<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AuthenticatorInterface;
use Psr\Http\Message\RequestInterface;

final class QueryAuthenticator implements AuthenticatorInterface
{
    /**
     * @var string
     */
    private $parameter;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $parameter, string $value)
    {
        $this->parameter = $parameter;
        $this->value = $value;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $uri = $request->getUri();
        \parse_str($uri->getQuery(), $query);

        $query[$this->parameter] = $this->value;

        return $request->withUri($uri->withQuery(\http_build_query($query)));
    }
}
