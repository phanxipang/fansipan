<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Tests\Services\HTTPBin\Connector;

class ConnectorTest extends TestCase
{
    public function test_middleware()
    {
        $connector = new Connector();

        $this->assertCount(1, $connector->middleware());

        $connector->middleware()->push(function () {
            echo '123';
        }, 'echo');

        $this->assertCount(2, $connector->middleware());

        $connector->middleware()->before('echo', function () {
        });

        $connector->middleware()->after('echo', function () {
        });

        $connector->middleware()->prepend(function () {
        }, 'first');

        $this->assertCount(5, $connector->middleware());

        $middleware = $connector->middleware()->all();

        $this->assertSame('first', $middleware[0][1]);
        $this->assertSame('echo', $middleware[3][1]);
    }
}
