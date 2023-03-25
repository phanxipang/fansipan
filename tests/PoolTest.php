<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Client\ReactBrowserClient;
use Jenky\Atlas\Pool;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetStatusRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetUuidRequest;
use React\Http\Browser;

final class PoolTest extends TestCase
{
    public function test_pool(): void
    {
        $connector = new Connector();

        $pool = new Pool($connector);

        $pool->send([
            new GetHeadersRequest(),
            new GetStatusRequest(),
            new GetUuidRequest(),
        ])->then(function ($r) {
            dump($r);
        });

        $this->assertTrue(true);
    }
}
