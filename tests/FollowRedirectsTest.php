<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Exception\TooManyRedirectsException;
use Jenky\Atlas\FollowRedirectsConnector;
use Jenky\Atlas\Middleware\FollowRedirects;
use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\NullConnector;
use Jenky\Atlas\Tests\Services\DummyRequest;

final class FollowRedirectsTest extends TestCase
{
    public function test_follow_redirects_without_middleware(): void
    {
        $client = new MockClient([
            MockResponse::create('', 301, ['Location' => 'http://localhost']),
            MockResponse::create(''),
        ]);

        $connector = (new NullConnector())->withClient($client);

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->redirect());
        $this->assertSame(301, $response->status());
    }

    public function test_follow_redirects_with_middleware(): void
    {
        $client = new MockClient([
            MockResponse::create('', 301, ['Location' => 'http://localhost']),
            MockResponse::create('', 302, ['Location' => 'http://localhost']),
            MockResponse::create(''),
        ]);

        $connector = new FollowRedirectsConnector(
            (new NullConnector())->withClient($client)
        );

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->ok());
        $client->assertSentCount(3);
    }

    public function test_too_many_redirects(): void
    {
        $client = new MockClient([
            MockResponse::create('', 301, ['Location' => 'http://localhost']),
            MockResponse::create('', 302, ['Location' => 'http://localhost']),
            MockResponse::create(''),
        ]);

        $connector = (new NullConnector())->withClient($client);
        $connector->middleware()->push(new FollowRedirects(1));

        $this->expectException(TooManyRedirectsException::class);

        $connector->send(new DummyRequest('http://localhost'));
    }

    public function test_follow_redirects_without_location_header(): void
    {
        $client = new MockClient([
            MockResponse::create('', 301),
            MockResponse::create(''),
        ]);

        $connector = (new NullConnector())->withClient($client);
        $connector->middleware()->push(new FollowRedirects());

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertSame(301, $response->status());
    }
}
