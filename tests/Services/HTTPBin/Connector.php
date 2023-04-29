<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Traits\ConnectorTrait;
use Jenky\Atlas\Traits\HasRequestCollection;

class Connector implements ConnectorInterface
{
    use ConnectorTrait;
    use HasRequestCollection;

    protected $requests = [
        GetHeadersRequest::class,
        'dynamic' => [
            'uuid' => GetUuidRequest::class,
            'delay' => DelayRequest::class,
        ],
    ];

    public function baseUri(): ?string
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
