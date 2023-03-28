<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Connector as BaseConnector;
use Jenky\Atlas\Traits\HasRequestCollection;

class Connector extends BaseConnector
{
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
