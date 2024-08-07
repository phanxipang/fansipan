<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\ConnectorConfigurator;
use Fansipan\Exception\TooManyRedirectsException;
use Fansipan\Middleware\FollowRedirects;
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;
use Fansipan\Tests\Services\DummyRequest;
use Fansipan\Tests\Services\GenericConnector;

final class FollowRedirectsTest extends TestCase
{
    public function test_follow_redirects_without_middleware(): void
    {
        $client = new MockClient([
            MockResponse::create('', 301, ['Location' => 'http://localhost']),
            MockResponse::create(''),
        ]);

        $connector = (new GenericConnector())->withClient($client);

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

        $connector = (new ConnectorConfigurator())
            ->followRedirects()
            ->configure((new GenericConnector())->withClient($client));

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

        $connector = (new GenericConnector())->withClient($client);
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

        $connector = (new GenericConnector())->withClient($client);
        $connector = (new ConnectorConfigurator())
            ->followRedirects()
            ->configure((new GenericConnector())->withClient($client));

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertSame(301, $response->status());
    }
}
