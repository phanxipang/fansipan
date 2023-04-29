<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Exceptions\RequestRetryFailedException;
use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\Retry\RetryCallback;
use Jenky\Atlas\Tests\Services\HTTPBin\GetStatusRequest;
use Jenky\Atlas\Tests\Services\HTTPBin\RetryableConnector;
use PHPUnit\Framework\TestCase;

final class RetryingRequestTest extends TestCase
{
    public function test_retryable_request(): void
    {
        $connector = new RetryableConnector();

        $this->expectException(RequestRetryFailedException::class);
        $this->expectExceptionMessage('Maximum 3 retries reached.');

        $connector->retry()->send(new GetStatusRequest(503));
    }

    public function test_retryalbe_request_with_custom_strategy(): void
    {
        $client = new MockClient(
            MockResponse::create('', 502)
        );
        $connector = (new RetryableConnector())->withClient($client);

        $response = $connector->retry(3, null, false)->send(new GetStatusRequest(502));

        $this->assertSame(502, $response->status());
        $this->assertTrue($response->serverError());

        $this->expectException(RequestRetryFailedException::class);
        $this->expectExceptionMessage('Maximum 2 retries reached.');

        $connector->retry(2, RetryCallback::when(function ($request, $response) {
            return true;
        }, 1000, 1.0))->send(new GetStatusRequest(502));

        $client->assertSentCount(2);
    }

    public function test_retry_with_successful_attempts(): void
    {
        $responses = function () {
            yield MockResponse::create('', 503);
            yield MockResponse::create('', 502);
            yield MockResponse::create('', 200);
        };

        $client = new MockClient($responses());
        $connector = (new RetryableConnector())->withClient($client);

        $response = $connector->retry()->send(new GetStatusRequest());
        $recorded = $client->recorded();

        $client->assertSentCount(3);

        $this->assertTrue($response->ok());
        $this->assertSame(503, $recorded[0][1]->getStatusCode());
        $this->assertSame(502, $recorded[1][1]->getStatusCode());
    }
}
