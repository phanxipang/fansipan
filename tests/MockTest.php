<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Http\Discovery\Psr17FactoryDiscovery;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\Tests\Services\PostmanEcho\EchoConnector;

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
        $connector = (new EchoConnector())
            ->fake();

        $response = $connector->post();

        $this->assertSame(200, $response->status());
    }

    public function test_fake_failed_response(): void
    {
        $connector = (new EchoConnector())
            ->fake($this->responseFactory->createResponse(500));

        $response = $connector->get();

        $this->assertSame(500, $response->status());

        $response = $connector->post();

        $this->assertSame(500, $response->status());
    }

    public function test_fake_sequence_responses(): void
    {
        $connector = (new EchoConnector())
            ->fake([
                MockResponse::make(['ok' => true]),
                MockResponse::make(['error' => 'Unauthenticated'], 401),
                $this->responseFactory->createResponse(502),
            ]);

        $request1 = $connector->get();
        $request2 = $connector->post();

        $this->assertTrue($request1->ok());
        $this->assertTrue($request1->data()['ok']);

        $this->assertTrue($request2->clientError());
        $this->assertSame('Unauthenticated', $request2->data()['error']);

        $this->assertTrue($connector->get()->serverError());
    }
}
