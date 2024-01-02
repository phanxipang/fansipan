<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\GenericConnector;
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;
use Fansipan\Tests\Services\JsonPlaceholder\GetUserRequest;
use Fansipan\Tests\Services\JsonPlaceholder\User;

final class MapperTest extends TestCase
{
    public function test_mapper(): void
    {
        $client = new MockClient([
            MockResponse::fixture(__DIR__.'/fixtures/user.json'),
        ]);

        $connector = (new GenericConnector())->withClient($client);
        $response = $connector->send(new GetUserRequest(1));

        $user = $response->object();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
        $this->assertSame('Leanne Graham', $user->name);
    }
}
