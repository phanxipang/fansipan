<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Body\Multipart;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\DTO\Uuid;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetUuidRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PostAnythingRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PostRequest;

class RequestTest extends TestCase
{
    public function test_sending_request_directly(): void
    {
        $request = new GetHeadersRequest();

        $this->expectException(\Exception::class);

        $request->send();

        $response = $request->withConnector(Connector::class)->send();

        $this->assertTrue($response->ok());
    }

    public function test_sending_request_from_connector(): void
    {
        $connector = new Connector();

        $response = $connector->send(new GetHeadersRequest());

        $this->assertTrue($response->ok());
    }

    public function test_request_headers(): void
    {
        $request = new GetHeadersRequest();

        $request->headers()
            ->with('Accept', 'application/json')
            ->with('X-Foo', 'bar');

        $response = $request->withConnector(Connector::class)->send();

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $response->json('headers', [])['X-Foo'] ?? null);
        $this->assertSame('atlas', $response->json('headers', [])['X-From'] ?? null);
    }

    public function test_cast_json_to_dto(): void
    {
        $request = new GetUuidRequest();

        $response = $request->withConnector(Connector::class)->send();

        $this->assertTrue($response->ok());
        $this->assertInstanceOf(Uuid::class, $dto = $response->dto());
        $this->assertSame($response->json('uuid'), $dto->uuid());
    }

    public function test_request_body(): void
    {
        $request = new PostAnythingRequest();

        $request->withConnector(Connector::class);

        $request->body()
            ->with('hello', 'world')
            ->merge(['foo' => 'bar'], ['buzz' => 'quiz']);

        $response = $request->send();

        $data = $response->json('json', []);

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $data['foo'] ?? null);
        $this->assertSame('quiz', $data['buzz'] ?? null);
        $this->assertSame('world', $data['hello'] ?? null);
    }

    public function test_request_multipart(): void
    {
        $request = new PostRequest('John', 'john.doe@example.com');
        $request->body()
            ->with('img', new Multipart(__DIR__.'/fixtures/1x1.png'));

        $response = $request->send();

        $this->assertFalse($response->failed());

        $data = $response->json('form', []);

        $this->assertSame('John', $data['name'] ?? null);
        $this->assertSame('john.doe@example.com', $data['email'] ?? null);
        $this->assertArrayHasKey('img', $response->json('files', []));
    }
}
