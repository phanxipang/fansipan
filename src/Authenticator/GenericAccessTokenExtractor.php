<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AccessTokenExtractorInterface;
use Fansipan\Decoder\ChainDecoder;
use Fansipan\Response;
use Psr\Http\Message\ResponseInterface;

final class GenericAccessTokenExtractor implements AccessTokenExtractorInterface
{
    /**
     * @var string
     */
    private $tokenKey;

    /**
     * @var string
     */
    private $expiresKey;

    public function __construct(
        string $tokenKey = 'access_token',
        string $expiresKey = 'expires'
    ) {
        $this->tokenKey = $tokenKey;
        $this->expiresKey = $expiresKey;
    }

    public function getToken(ResponseInterface $response): AccessToken
    {
        $responseWrapper = new Response($response, ChainDecoder::default());

        return new AccessToken(
            $responseWrapper->data()[$this->tokenKey] ?? null,
            $responseWrapper->data()[$this->expiresKey] ?? null
        );
    }
}
