<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\Decoder\ChainDecoder;
use Fansipan\Exception\NotDecodableException;
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;
use Fansipan\Response;
use Fansipan\Tests\Services\HTTPBin\Connector;
use Fansipan\Tests\Services\HTTPBin\GetStatusRequest;

final class ResponseTest extends TestCase
{
    public function test_response_array_access(): void
    {
        $client = new MockClient([
            MockResponse::fixture(__DIR__.'/fixtures/user.json'),
        ]);
        $connector = (new Connector())->withClient($client);
        $response = $connector->send(new GetStatusRequest());

        $this->assertArrayHasKey('Content-Type', $response->headers());
        $this->assertSame('OK', $response->reason());
        $this->assertTrue($response->ok());
        $this->assertSame(1, $response['id'] ?? null);

        $this->expectException(\LogicException::class);

        $response['foo'] = 'bar';
        unset($response['id']);
    }

    public function test_response_assertions(): void
    {
        $client = new MockClient([
            MockResponse::create('', 400),
            MockResponse::create('', 401),
            MockResponse::create('', 403),
        ]);
        $connector = (new Connector())->withClient($client);

        $response = $connector->send(new GetStatusRequest());

        $this->assertTrue($response->clientError());

        $response = $connector->send(new GetStatusRequest());

        $this->assertTrue($response->unauthorized());
        $this->assertFalse($response->serverError());

        $response = $connector->send(new GetStatusRequest());

        $this->assertTrue($response->forbidden());
        $this->assertTrue($response->failed());
    }

    public function test_response_macro(): void
    {
        $response = new Response(MockResponse::create(''));

        $response->macro('foo', function () {
            return 'bar';
        });

        $this->assertSame('bar', $response->foo());
    }

    public function test_response_can_be_json_serialize(): void
    {
        $client = new MockClient(
            MockResponse::fixture($file = __DIR__.'/fixtures/user.json')
        );
        $connector = (new Connector())->withClient($client);
        $response = $connector->send(new GetStatusRequest());

        $this->assertTrue($response->ok());
        $this->assertJson($json = \json_encode($response));
        $this->assertJsonStringEqualsJsonFile($file, $json);
    }

    public function test_response_without_decoder(): void
    {
        $response = new Response(MockResponse::create(''));

        $this->expectException(NotDecodableException::class);

        $response->decode();

        $this->assertIsArray($response->data());
    }

    public function test_response_unable_to_decode(): void
    {
        $response = new Response(MockResponse::create('Hello'), ChainDecoder::default());

        $this->assertSame([], $response->data());
    }
}
