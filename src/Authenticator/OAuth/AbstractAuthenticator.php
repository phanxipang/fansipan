<?php

declare(strict_types=1);

namespace Fansipan\Authenticator\OAuth;

use Fansipan\Authenticator\AccessToken;
use Fansipan\Authenticator\GenericAccessTokenExtractor;
use Fansipan\Contracts\AccessTokenExtractorInterface;
use Fansipan\Contracts\AuthenticatorInterface;
use Fansipan\GenericRequest;
use Fansipan\Util;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractAuthenticator implements AuthenticatorInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var \Fansipan\Contracts\AccessTokenExtractorInterface
     */
    protected $extractor;

    public function __construct(
        Config $config,
        ?ClientInterface $client = null,
        ?AccessTokenExtractorInterface $extractor = null
    ) {
        $this->config = $config;
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->extractor = $extractor ?? new GenericAccessTokenExtractor();
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', 'Bearer '.$this->getAccessToken());
    }

    protected function getAccessToken(): AccessToken
    {
        $request = new GenericRequest($this->config->tokenEndpoint(), 'POST');
        $request->body()
            ->set([
                'grant_type' => $this->grandType(),
                'client_id' => $this->config->clientId(),
                'client_secret' => $this->config->clientSecret(),
                'scope' => $this->config->scopes(true),
            ]);

        $response = $this->client->sendRequest(Util::request($request));

        return $this->extractor->extract($response);
    }
}
