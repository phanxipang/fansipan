<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\DTO\Uuid;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetUuidRequest;

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
            ->set('Accept', 'application/json')
            ->set('X-Foo', 'bar');

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
}
