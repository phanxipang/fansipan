<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use GuzzleHttp\Client;
use Jenky\Atlas\Connector as BaseConnector;
use Psr\Http\Client\ClientInterface;

class Connector extends BaseConnector
{
    protected $requests = [
        GetHeadersRequest::class,
        'dynamic' => [
            'uuid' => GetUuidRequest::class,
            'delay' => DelayRequest::class,
        ],
    ];

    protected function defineClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://httpbin.org',
        ]);
    }

    protected function defaultMiddleware(): array
    {
        return [
            Middleware\AddCustomHeader::class,
        ];
    }
}
