<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Exception\InvalidTokenException;
use Psr\Http\Message\RequestInterface;

trait HeaderAuthorizationTrait
{
    /**
     * @var string|\Stringable
     */
    protected $token;

    public function authenticate(RequestInterface $request): RequestInterface
    {
        if (! $this->token) {
            throw new InvalidTokenException('Invalid authorization token.');
        }

        return $request->withAddedHeader('Authorization', (string) $this->token);
    }
}
