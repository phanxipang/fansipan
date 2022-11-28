<?php

namespace Jenky\Atlas\Tests;

use GuzzleHttp\Client;
use Http\Factory\Discovery\ClientLocator;
use Http\Factory\Discovery\HttpClient;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Client\ClientInterface;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ClientLocator::register(ClientInterface::class, Client::class);
        HttpClient::clearCache();
    }
}
