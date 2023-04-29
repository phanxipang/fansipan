<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Http\Discovery\Psr17FactoryDiscovery;
use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\Mock\ScopingMockClient;
use Jenky\Atlas\Mock\Uri;
use Jenky\Atlas\Tests\Services\JsonPlaceholder\UserRequest;
use Jenky\Atlas\Tests\Services\PostmanEcho\EchoConnector;
use Psr\Http\Message\RequestInterface;

final class MockTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    private $responseFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseFactory = Psr17FactoryDiscovery::findResponseFactory();
    }

    public function test_fake_default_response(): void
    {
        $client = new MockClient();

        $connector = (new EchoConnector())->withClient($client);

        $response = $connector->post();

        $this->assertSame(200, $response->status());
    }

    public function test_fake_failed_response(): void
    {
        $client = new MockClient($this->responseFactory->createResponse(500));

        $connector = (new EchoConnector())->withClient($client);

        $response = $connector->get();

        $this->assertSame(500, $response->status());

        $response = $connector->post();

        $this->assertSame(500, $response->status());
    }

    public function test_fake_sequence_responses(): void
    {
        $client = new MockClient([
            MockResponse::create(['ok' => true]),
            MockResponse::create(['error' => 'Unauthenticated'], 401),
            $this->responseFactory->createResponse(502),
        ]);

        $connector = (new EchoConnector())->withClient($client);

        $request1 = $connector->get();
        $request2 = $connector->post();

        $this->assertTrue($request1->ok());
        $this->assertTrue($request1->data()['ok']);

        $this->assertTrue($request2->clientError());
        $this->assertSame('Unauthenticated', $request2->data()['error']);

        $this->assertTrue($connector->get()->serverError());

        $client->assertSent('/get');
    }

    public function test_fake_conditional_responses(): void
    {
        $client = new ScopingMockClient([
            'jsonplaceholder.typicode.com/users/*' => MockResponse::fixture(__DIR__.'/fixtures/user.json'),
            'postman-echo.com/cookies*' => MockResponse::create('', 400),
            '*' => MockResponse::create('', 200),
        ]);

        $connector = (new EchoConnector())->withClient($client);

        $this->assertSame(200, $connector->get()->status());
        $this->assertTrue($connector->cookies()->get()->clientError());

        $client->assertSent(static function (RequestInterface $request): bool {
            return $request->getMethod() === 'GET' && Uri::matches('/cookies', (string) $request->getUri());
        });

        $client->assertNotSent('/users/*');

        $response = (new UserRequest(1))->send();

        $this->assertTrue($response->ok());
        $this->assertSame('Leanne Graham', $response->data()['name'] ?? '');
        $this->assertSame('Bret', $response->data()['username'] ?? '');
    }
}
