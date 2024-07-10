<?php

declare(strict_types=1);

namespace Fansipan\Authenticator;

class AccessToken implements \Stringable
{
    /**
     * @var string|\Stringable
     */
    private $token;

    /**
     * @var ?\DateTimeInterface
     */
    private $expires;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param  string|\Stringable $token
     * @param  null|int|\DateTimeInterface $expires
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($token, $expires, array $attributes = [])
    {
        if (! \is_string($token) && ! $token instanceof \Stringable) {
            throw new \InvalidArgumentException(sprintf('Token must be string or object that implements __toString(), "%s" given.', \get_debug_type($token)));
        }

        $this->token = $token;

        if (\is_null($expires)) {
            $this->expires = null;
        } else {
            if (! \is_numeric($expires) && ! $expires instanceof \DateTimeInterface) {
                throw new \InvalidArgumentException(sprintf('Expires must be integer or object that implements \DateTimeInterface, "%s" given.', \get_debug_type($token)));
            }

            $this->expires = \is_numeric($expires) ? (new \DateTimeImmutable())->setTimestamp($expires) : $expires;
        }

        $this->attributes = $attributes;
    }

    /**
     * @return string|\Stringable
     */
    public function token()
    {
        return $this->token;
    }

    public function expires(): ?\DateTimeInterface
    {
        return $this->expires;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function toHeader(): string
    {
        return 'Bearer '.\trim((string) $this);
    }

    public function __toString()
    {
        return (string) $this->token();
    }
}
