<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Pool\AmphpPool;
use Jenky\Atlas\Pool\ReactPool;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetStatusRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetUuidRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PoolableConnector;

final class PoolTest extends TestCase
{
    /**
     * @var PoolableConnector
     */
    private $connector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connector = new PoolableConnector();
    }

    public function test_pool(): void
    {
        $connector = $this->connector->withClient(new MockClient());

        $responses = $connector->pool([
            new GetHeadersRequest(),
            new GetStatusRequest(),
            new GetUuidRequest(),
        ])->send();

        $this->assertCount(3, $responses);
    }

    public function test_amphp_pool(): void
    {
        $connector = $this->connector->withClient(new MockClient());

        $pool = $connector->pool([
            function () use ($connector): Response {
                return $connector->send(new GetHeadersRequest());
            },
            function () use ($connector): Response {
                return $connector->send(new GetStatusRequest());
            },
            function () use ($connector): Response {
                return $connector->send(new GetUuidRequest());
            },
        ], $amp = new AmphpPool());

        $this->assertSame($amp, $pool);
        $this->assertCount(3, $pool->send());
    }

    public function test_react_pool(): void
    {
        $connector = $this->connector->withClient(new MockClient());

        $pool = $connector->pool([
            'a' => new GetHeadersRequest(),
            'b' => new GetStatusRequest(),
            'c' => function () use ($connector): Response {
                return $connector->send(new GetUuidRequest());
            },
        ], $react = new ReactPool());

        $this->assertSame($react, $pool);
        $this->assertCount(3, $responses = $pool->send());

        $this->assertArrayHasKey('a', $responses);
        $this->assertArrayHasKey('b', $responses);
        $this->assertArrayHasKey('c', $responses);

        $this->assertInstanceOf(Response::class, $responses['a']);
        $this->assertInstanceOf(Response::class, $responses['b']);
        $this->assertInstanceOf(Response::class, $responses['c']);
    }
}
