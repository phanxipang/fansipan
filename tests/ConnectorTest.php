<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Middleware\Interceptor;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\PostmanEcho\EchoConnector;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ConnectorTest extends TestCase
{
    public function test_middleware(): void
    {
        $connector = new Connector();

        $this->assertCount(1, $connector->middleware());

        $connector->middleware()->push(static function (RequestInterface $request, callable $next) {
            return $next($request->withHeader('Echo', 'Atlas'));
        }, 'echo');

        $this->assertCount(2, $connector->middleware());

        $id = uniqid();

        $connector->middleware()->before('echo', Interceptor::request(static function (RequestInterface $request) use ($id) {
            return $request->withHeader('X-Unique-Id', $id);
        }));

        $connector->middleware()->after('echo', Interceptor::response(static function (ResponseInterface $response) use ($id) {
            return $response->withHeader('X-Unique-Id', $id);
        }));

        $connector->middleware()->prepend(static function (RequestInterface $request, callable $next) {
            return $next($request);
        }, 'first');

        $this->assertCount(5, $connector->middleware());

        $middleware = $connector->middleware()->all();

        $this->assertSame('first', $middleware[0][1]);
        $this->assertSame('echo', $middleware[3][1]);

        $connector->middleware()->remove('first');

        $this->assertCount(4, $connector->middleware());

        $connector->middleware()->push(static function (RequestInterface $request, callable $next) {
            return $next($request->withHeader('X-Foo', 'bar'));
        });

        $connector->middleware()->remove('echo');

        $response = $connector->send(new GetHeadersRequest());

        $this->assertTrue($response->successful());
        $this->assertSame('bar', $response->data()['headers']['X-Foo'] ?? null);
        $this->assertSame($id, $response->data()['headers']['X-Unique-Id'] ?? null);
        $this->assertSame($id, $response->header('X-Unique-Id'));
        $this->assertArrayNotHasKey('Echo', $response->data()['headers'] ?? []);
    }

    public function test_requests_can_be_called_via_magic_method(): void
    {
        $connector = new Connector();

        $response = $connector->getHeadersRequest();

        $this->assertTrue($response->ok());

        $response = $connector->dynamic()->uuid();

        $this->assertTrue($response->ok());

        $response = $connector->dynamic()->delay(2);

        $this->assertTrue($response->ok());
        $this->assertSame('https://httpbin.org/delay/2', $response->data()['url'] ?? null);
    }

    public function test_requests_without_magic_method(): void
    {
        $echo = new EchoConnector();

        $this->assertTrue($echo->get()->ok());
        $this->assertTrue($echo->post()->ok());

        $this->assertSame(200, $echo->cookies()->get()->status());

        $response = $echo->cookies()->set(['foo' => 'bar']);

        $this->assertTrue($response->redirect());
    }
}
