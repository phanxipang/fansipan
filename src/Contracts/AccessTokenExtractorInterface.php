<?php

declare(strict_types=1);

namespace Fansipan\Contracts;

use Fansipan\Authenticator\AccessToken;
use Psr\Http\Message\ResponseInterface;

interface AccessTokenExtractorInterface
{
    public function getToken(ResponseInterface $response): AccessToken;
}
