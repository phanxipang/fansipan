<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use GuzzleHttp\Client;
use Jenky\Atlas\Connector;
use Jenky\Atlas\Contracts\RetryableInterface;
use Jenky\Atlas\Traits\Retryable;
use Psr\Http\Client\ClientInterface;

class RetryableConnector extends Connector implements RetryableInterface
{
    use Retryable;

    protected function defineClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://httpbin.org',
        ]);
    }
}
