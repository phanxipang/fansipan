<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\Contracts\MapperInterface;
use Fansipan\GenericConnector;
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;
use Fansipan\Tests\Services\DummyRequest;
use Fansipan\Tests\Services\JsonPlaceholder\Error;
use Fansipan\Tests\Services\JsonPlaceholder\GetUserRequest;
use Fansipan\Tests\Services\JsonPlaceholder\User;

final class MapperTest extends TestCase
{
    public function test_mapper(): void
    {
        $client = new MockClient([
            MockResponse::fixture(__DIR__.'/fixtures/user.json'),
            MockResponse::create('', 500),
        ]);

        $connector = (new GenericConnector())->withClient($client);
        $response = $connector->send($request = new GetUserRequest(1));

        $this->assertInstanceOf(MapperInterface::class, $request->decoder());

        $user = $response->object();

        $this->assertTrue($response->ok());
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
        $this->assertSame('Leanne Graham', $user->name);

        $response = $connector->send(new GetUserRequest(0));

        $this->assertTrue($response->failed());
        $this->assertInstanceOf(Error::class, $response->object());
    }

    public function test_decoder_is_not_mapper(): void
    {
        $client = new MockClient();
        $connector = (new GenericConnector())->withClient($client);
        $response = $connector->send($request = new DummyRequest('/'));

        $this->assertNotInstanceOf(MapperInterface::class, $request->decoder());
        $this->assertTrue($response->ok());
        $this->assertNull($response->object());
    }
}
