<?php

declare(strict_types=1);

namespace Fansipan\Authenticator\OAuth;

class Config
{
    /**
     * @var non-empty-string
     */
    private $clientId;

    /**
     * @var non-empty-string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var non-empty-string
     */
    private $authorizeEndpoint;

    /**
     * @var non-empty-string
     */
    private $tokenEndpoint;

    /**
     * @var string[]
     */
    private $scopes = [];

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri = '',
        string $authorizeEndpoint = 'authorize',
        string $tokenEndpoint = 'token'
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->authorizeEndpoint = $authorizeEndpoint;
        $this->tokenEndpoint = $tokenEndpoint;
    }

    public function clientId(): string
    {
        return $this->clientId;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret;
    }

    public function redirectUri(): string
    {
        return $this->redirectUri;
    }

    public function withRedirectUri(string $uri): self
    {
        $clone = clone $this;
        $clone->redirectUri = $uri;

        return $clone;
    }

    public function authorizeEndpoint(): string
    {
        return $this->authorizeEndpoint;
    }

    public function tokenEndpoint(): string
    {
        return $this->tokenEndpoint;
    }

    /**
     * @return ($asString is true ? string : string[])
     */
    public function scopes(bool $asString = false, string $separator = ' ')
    {
        $scopes = \array_unique($this->scopes);

        return $asString ? $scopes : \implode($separator, $scopes);
    }

    public function withScopes(string ...$scopes): self
    {
        $clone = clone $this;
        $clone->scopes = \array_merge($this->scopes, $scopes);

        return $clone;
    }
}
