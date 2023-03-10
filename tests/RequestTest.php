<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Body\Multipart;
use Jenky\Atlas\Exceptions\HttpException;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\DTO\Uuid;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetStatusRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetUuidRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetXmlRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PostAnythingRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PostRequest;

class RequestTest extends TestCase
{
    /**
     * @var \Jenky\Atlas\Tests\Services\HTTPBin\Connector
     */
    private $connector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connector = new Connector();
    }

    public function test_sending_request_directly()
    {
        $request = new EchoRequest();

        $response = $request->send();

        $this->assertTrue($response->ok());
    }

    public function test_sending_request_from_connector()
    {
        $response = $this->connector->send(new GetHeadersRequest());

        $this->assertTrue($response->ok());
    }

    public function test_request_headers()
    {
        $request = new GetHeadersRequest();

        $request->headers()
            ->with('Accept', 'application/json')
            ->with('X-Foo', 'bar');

        $response = $this->connector->send($request);

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $response->data()['headers']['X-Foo'] ?? null);
        $this->assertSame('atlas', $response->data()['headers']['X-From'] ?? null);
    }

    public function test_cast_response_to_dto()
    {
        $request = new GetUuidRequest();

        $response = $this->connector->send($request);

        $this->assertTrue($response->ok());
        $this->assertInstanceOf(Uuid::class, $dto = Uuid::fromResponse($response));
        $this->assertSame($response->data()['uuid'] ?? null, $dto->uuid());
    }

    public function test_request_body()
    {
        $request = new PostAnythingRequest();

        $request->body()
            ->with('hello', 'world')
            ->merge(['foo' => 'bar'], ['buzz' => 'quiz']);

        $response = $this->connector->send($request);

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $response['json']['foo'] ?? null);
        $this->assertSame('quiz', $response['json']['buzz'] ?? null);
        $this->assertSame('world', $response['json']['hello'] ?? null);
    }

    public function test_request_multipart()
    {
        $request = new PostRequest('John', 'john.doe@example.com');
        $request->body()
            ->with('img', new Multipart(__DIR__.'/fixtures/1x1.png'));

        $response = $this->connector->send($request);

        $this->assertFalse($response->failed());

        $data = $response->data()['form'] ?? [];

        $this->assertSame('John', $data['name'] ?? null);
        $this->assertSame('john.doe@example.com', $data['email'] ?? null);
        $this->assertArrayHasKey('img', $response->data()['files'] ?? []);
    }

    public function test_response_xml_decoder()
    {
        $request = new GetXmlRequest();

        $response = $this->connector->send($request);

        $this->assertTrue($response->ok());

        $this->assertIsArray($data = $response->data());
        $this->assertCount(2, $data['slide']);
    }

    public function test_response_exception()
    {
        $request = new GetStatusRequest(400);

        $this->expectException(HttpException::class);

        $this->connector->send($request)->throwIf(function (Response $response) {
            return $response->failed();
        });

        $this->connector->send($request->withStatus(200))->throwIf(true);
    }
}
