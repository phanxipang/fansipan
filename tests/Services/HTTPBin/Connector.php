<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Traits\ConnectorTrait;

class Connector implements ConnectorInterface
{
    use ConnectorTrait;
    use Retryable;

    public static function baseUri(): ?string
    {
        return 'https://httpbin.org';
    }

    protected function defaultMiddleware(): array
    {
        return [
            new Middleware\AddCustomHeader(),
        ];
    }
}
