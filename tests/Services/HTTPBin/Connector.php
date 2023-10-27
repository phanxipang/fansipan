<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;

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
