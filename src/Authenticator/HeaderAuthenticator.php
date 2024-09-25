<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

use Fansipan\Contracts\AuthenticatorInterface;
use Psr\Http\Message\RequestInterface;

final class HeaderAuthenticator implements AuthenticatorInterface
{
    /**
     * @var string
     */
    private $header;

    /**
     * @var string|\Stringable
     */
    private $value;

    /**
     * @param  string|\Stringable $value
     */
    public function __construct(string $header, $value)
    {
        $this->header = $header;
        $this->value = $value;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withAddedHeader($this->header, (string) $this->value);
    }
}
