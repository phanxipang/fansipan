<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Exception\InvalidTokenException;
use Psr\Http\Message\RequestInterface;

trait HeaderAuthorizationTrait
{
    /**
     * @var string
     */
    private $header = 'Authorization';

    /**
     * @var string|\Stringable
     */
    protected $value;

    public function authenticate(RequestInterface $request): RequestInterface
    {
        if (! $this->value) {
            throw new InvalidTokenException('Invalid authorization token.');
        }

        return $request->withAddedHeader($this->header, (string) $this->value);
    }
}
