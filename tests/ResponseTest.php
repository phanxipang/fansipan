<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\Tests\Services\HTTPBin\Connector;
use Jenky\Atlas\Tests\Services\HTTPBin\GetStatusRequest;

final class ResponseTest extends TestCase
{
    public function test_response_array_access(): void
    {
        $client = new MockClient([
            MockResponse::fixture(__DIR__.'/fixtures/user.json'),
        ]);
        $connector = (new Connector())->withClient($client);
        $response = $connector->send(new GetStatusRequest());

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
}
