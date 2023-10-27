<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\ConnectorConfigurator;
use Fansipan\Exception\RequestRetryFailedException;
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;
use Fansipan\Retry\Backoff;
use Fansipan\Retry\RetryCallback;
use Fansipan\Tests\Services\HTTPBin\Connector;
use Fansipan\Tests\Services\HTTPBin\GetStatusRequest;
use PHPUnit\Framework\TestCase;

final class RetryRequestsTest extends TestCase
{
    public function test_retry_requests(): void
    {
        $connector = new Connector();

        $this->expectException(RequestRetryFailedException::class);
        $this->expectExceptionMessage('Maximum 3 retries reached.');

        $connector->retry()->send(new GetStatusRequest(503));
    }

    public function test_retry_requests_with_custom_strategy(): void
    {
        $client = new MockClient(
            MockResponse::create('', 502)
        );
        $connector = (new Connector())->withClient($client);

        $response = $connector->retry(3, null, false)->send(new GetStatusRequest(502));

        $this->assertSame(502, $response->status());
        $this->assertTrue($response->serverError());

        $this->expectException(RequestRetryFailedException::class);
        $this->expectExceptionMessage('Maximum 2 retries reached.');

        $connector->retry(2, RetryCallback::when(static function () {
            return true;
        }, 1000, 1.0))->send(new GetStatusRequest(502));

        $client->assertSentCount(2);

        $connector->retry(3, RetryCallback::when(static function () {
            return true;
        })->withDelay(new Backoff([1000, 2000, 3000])))->send(new GetStatusRequest(502));

        $client->assertSentCount(3);
    }

    public function test_retry_with_successful_attempts(): void
    {
        $responses = function () {
            yield MockResponse::create('', 503);
            yield MockResponse::create('', 502);
            yield MockResponse::create('', 200);
        };

        $client = new MockClient($responses());
        $connector = (new ConnectorConfigurator())
            ->retry()
            ->configure(
                $originalConnector = (new Connector())->withClient($client)
            );

        $this->assertCount(1, $originalConnector->middleware());

        $response = $connector->send(new GetStatusRequest());
        $recorded = $client->recorded();

        $client->assertSentCount(3);

        $this->assertTrue($response->ok());
        $this->assertSame(503, $recorded[0][1]->getStatusCode());
        $this->assertSame(502, $recorded[1][1]->getStatusCode());
    }
}
