<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Middleware\FollowRedirects;
use Jenky\Atlas\Traits\ConnectorDecoratorTrait;

class FollowRedirectsConnector implements ConnectorInterface
{
    use ConnectorDecoratorTrait;

    public function __construct(
        ConnectorInterface $connector,
        int $max = 5,
        array $protocols = ['http', 'https'],
        bool $strict = false,
        bool $referer = false
    ) {
        $clone = clone $connector;

        $clone->middleware()
            ->unshift(new FollowRedirects(
                $max, $protocols, $strict, $referer
            ));

        $this->connector = $clone;
    }
}
