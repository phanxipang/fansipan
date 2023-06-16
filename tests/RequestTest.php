<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Body\MultipartResource;
use Jenky\Atlas\Exception\HttpException;
use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\Response;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\DTO\Uuid;
use Jenky\Atlas\Tests\Services\HTTPBin\GetHeadersRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetStatusRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetUuidRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\GetXmlRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PostAnythingRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\PostRequest;
use Jenky\Atlas\Tests\Services\PostmanEcho\CurrentUtcRequest;

final class RequestTest extends TestCase
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

    public function test_sending_request_directly(): void
    {
        $request = new CurrentUtcRequest();

        $response = $request->send();

        $this->assertTrue($response->ok());

        $datetime = new \DateTime((string) $response);
        $this->assertSame((new \DateTime())->format('Y-m-d'), $datetime->format('Y-m-d'));
    }

    public function test_sending_request_from_connector(): void
    {
        $client = new MockClient();
        $connector = $this->connector->withClient($client);

        $response = $connector->send(new GetHeadersRequest());

        $this->assertTrue($response->ok());
    }

    public function test_request_headers(): void
    {
        $client = new MockClient(
            MockResponse::fixture(__DIR__.'/fixtures/headers.json')
        );
        $connector = $this->connector->withClient($client);

        $request = new GetHeadersRequest();

        $request->headers()
            ->with('Accept', 'application/json')
            ->with('X-Foo', 'bar');

        $response = $connector->send($request);

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $response->data()['headers']['X-Foo'] ?? null);
        $this->assertSame('atlas', $response->data()['headers']['X-From'] ?? null);
    }

    public function test_cast_response_to_dto(): void
    {
        $client = new MockClient(
            MockResponse::create(['uuid' => '01b67779-4690-4094-8e83-624cc496e1ef'])
        );
        $connector = $this->connector->withClient($client);

        $request = new GetUuidRequest();

        $response = $connector->send($request);

        $this->assertTrue($response->ok());
        $this->assertInstanceOf(Uuid::class, $dto = Uuid::fromResponse($response));
        $this->assertSame($response->data()['uuid'] ?? null, $dto->uuid());
    }

    public function test_request_body(): void
    {
        $client = new MockClient(
            MockResponse::fixture(__DIR__.'/fixtures/anything.json')
        );
        $connector = $this->connector->withClient($client);

        $request = new PostAnythingRequest();

        $request->body()
            ->with('hello', 'world')
            ->merge(['foo' => 'bar'], ['buzz' => 'quiz']);

        $response = $connector->send($request);

        $this->assertTrue($response->ok());
        $this->assertSame('bar', $response['json']['foo'] ?? null);
        $this->assertSame('quiz', $response['json']['buzz'] ?? null);
        $this->assertSame('world', $response['json']['hello'] ?? null);
    }

    public function test_request_multipart(): void
    {
        $client = new MockClient(
            MockResponse::fixture(__DIR__.'/fixtures/multipart.json')
        );
        $connector = $this->connector->withClient($client);

        $request = new PostRequest('John', 'john.doe@example.com');
        $request->body()
            ->with('img', MultipartResource::from(__DIR__.'/fixtures/1x1.png'));

        $response = $connector->send($request);

        $this->assertFalse($response->failed());

        $data = $response['form'] ?? [];

        $this->assertSame('John', $data['name'] ?? null);
        $this->assertSame('john.doe@example.com', $data['email'] ?? null);
        $this->assertArrayHasKey('img', $response->data()['files'] ?? []);
    }

    public function test_response_xml_decoder(): void
    {
        $client = new MockClient(
            MockResponse::fixture(__DIR__.'/fixtures/slideshow.xml', 200, ['Content-Type' => 'text/xml'])
        );
        $connector = $this->connector->withClient($client);

        $request = new GetXmlRequest();

        $response = $connector->send($request);

        $this->assertTrue($response->ok());

        $this->assertIsArray($data = $response->data());
        $this->assertCount(2, $data['slide']);
    }

    public function test_response_exception(): void
    {
        $client = new MockClient([
            MockResponse::create('', 400),
            MockResponse::create(''),
        ]);
        $connector = $this->connector->withClient($client);

        $request = new GetStatusRequest(400);

        $this->expectException(HttpException::class);

        $connector->send($request)->throwIf(function (Response $response) {
            return $response->failed();
        });

        $connector->send($request->withStatus(200))->throwIf(true);
    }
}
