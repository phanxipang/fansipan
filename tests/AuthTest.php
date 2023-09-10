<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Middleware\Auth\BasicAuthentication;
use Jenky\Atlas\Middleware\Auth\BearerAuthentication;
use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\GenericConnector;
use Jenky\Atlas\Tests\Services\DummyRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AuthTest extends TestCase
{
    private function fakeAuthentication(string $credential): callable
    {
        return static function (RequestInterface $request, callable $next) use ($credential): ResponseInterface {
            if (! $request->hasHeader('Authorization')) {
                return $next($request)->withStatus(401);
            }

            $authorization = $request->getHeaderLine('Authorization');

            if ($authorization !== $credential) {
                return $next($request)->withStatus(401);
            }

            return $next($request);
        };
    }

    private function fakeBasicAuthentication(string $username, string $password): callable
    {
        $credential = base64_encode("$username:$password");

        return $this->fakeAuthentication('Basic '.$credential);
    }

    private function fakeTokenAuthentication(string $token): callable
    {
        return $this->fakeAuthentication('Bearer '.$token);
    }

    public function test_basic_auth(): void
    {
        $client = new MockClient(
            MockResponse::create(['authenticated' => true])
        );
        $connector = (new GenericConnector())->withClient($client);
        $connector->middleware()->push($this->fakeBasicAuthentication('foo', 'password'), 'fake_auth');

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->unauthorized());

        $connector->middleware()->before('fake_auth', new BasicAuthentication('foo', 'password'));

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->ok());
        $this->assertTrue($response['authenticated']);
    }

    public function test_token_auth(): void
    {
        $client = new MockClient();
        $connector = (new GenericConnector())->withClient($client);
        $connector->middleware()->push($this->fakeTokenAuthentication('#zKh#4KNu$Bq4^b97KJ6'), 'fake_auth');

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->unauthorized());

        $connector->middleware()->before('fake_auth', new BearerAuthentication('#zKh#4KNu$Bq4^b97KJ6'));

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->ok());
    }
}
