<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Closure;
use Jenky\Atlas\Middleware\Interceptor;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;

class ConnectorTest extends TestCase
{
    public function test_middleware()
    {
        $connector = new Connector();

        $this->assertCount(1, $connector->middleware());

        $connector->middleware()->push(function (Request $request, Closure $next): Response {
            return $next($request);
        }, 'echo');

        $this->assertCount(2, $connector->middleware());

        $id = uniqid();

        $connector->middleware()->before('echo', Interceptor::request(function (Request $request) use ($id) {
            $request->headers()->with('X-Unique-Id', $id);
        }));

        $connector->middleware()->after('echo', Interceptor::response(function (Response $response) use ($id) {
            $response->withHeader('X-Unique-Id', $id);
        }));

        $connector->middleware()->prepend(function (Request $request, Closure $next): Response {
            return $next($request);
        }, 'first');

        $this->assertCount(5, $connector->middleware());

        $middleware = $connector->middleware()->all();

        $this->assertSame('first', $middleware[0][1]);
        $this->assertSame('echo', $middleware[3][1]);

        $connector->middleware()->push(function (Request $request, Closure $next): Response {
            $request->headers()->with('X-Foo', 'bar');

            return $next($request);
        });

        $response = $connector->send(new GetHeadersRequest());

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $response->data()['headers']['X-Foo'] ?? null);
        $this->assertSame($id, $response->data()['headers']['X-Unique-Id'] ?? null);
        $this->assertSame($id, $response->data()['headers']['X-Unique-Id'] ?? null);
    }

    public function test_requests_can_be_called_via_magic_method()
    {
        $connector = new Connector();

        $response = $connector->getHeadersRequest()->send();

        $this->assertTrue($response->ok());

        $response = $connector->dynamic()->uuid()->send();

        $this->assertTrue($response->ok());

        $response = $connector->dynamic()->delay(2)->send();

        $this->assertTrue($response->ok());
        $this->assertSame('https://httpbin.org/delay/2', $response->data()['url'] ?? null);
    }
}
