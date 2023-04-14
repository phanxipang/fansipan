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

        $this->expectException(RequestRetryFailedException::class);
        $this->expectExceptionMessage('Maximum 2 retries reached.');

        $connector->retry(2, RetryCallback::when(function ($request, $response) {
            return true;
        }, 1000, 2.0))->send(new GetStatusRequest(502));

        $client->assertSentCount(2);
    }
}
