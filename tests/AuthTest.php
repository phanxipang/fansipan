<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\Authenticator\BasicAuthenticator;
use Fansipan\Authenticator\BearerAuthenticator;
use Fansipan\Authenticator\HeaderAuthenticator;
use Fansipan\Authenticator\QueryAuthenticator;
use Fansipan\Contracts\AuthenticatorInterface;
use Fansipan\Middleware\Authentication;
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;
use Fansipan\Tests\Services\DummyRequest;
use Fansipan\Tests\Services\GenericConnector;
use Fansipan\Util;
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

        $connector->middleware()->before('fake_auth', new Authentication(new BasicAuthenticator('foo', 'password')));

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->ok());
        $this->assertTrue($response['authenticated']);
    }

    public function test_bearer_auth(): void
    {
        $client = new MockClient();
        $connector = (new GenericConnector())->withClient($client);
        $connector->middleware()->push($this->fakeTokenAuthentication('#zKh#4KNu$Bq4^b97KJ6'), 'fake_auth');

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->unauthorized());

        $connector->middleware()->before('fake_auth', new Authentication(new BearerAuthenticator('#zKh#4KNu$Bq4^b97KJ6')));

        $response = $connector->send(new DummyRequest('http://localhost'));

        $this->assertTrue($response->ok());
    }

    private function authenticateRequest(AuthenticatorInterface $authenticator): RequestInterface
    {
        return $authenticator->authenticate(Util::request(new DummyRequest('http://localhost')));
    }

    public function test_basic_authenticator(): void
    {
        $user = 'root';
        $pass = 'password';

        $request = $this->authenticateRequest(new BasicAuthenticator($user, $pass));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertSame(sprintf('Basic %s', \base64_encode($user.':'.$pass)), $request->getHeaderLine('Authorization'));
    }

    public function test_bearer_authenticator(): void
    {
        $token = 'token';

        $request = $this->authenticateRequest(new BearerAuthenticator($token));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertSame(sprintf('Bearer %s', $token), $request->getHeaderLine('Authorization'));
    }

    public function test_header_authenticator(): void
    {
        $header = 'X-API-KEY';
        $key = 'key';

        $request = $this->authenticateRequest(new HeaderAuthenticator($header, $key));

        $this->assertTrue($request->hasHeader($header));
        $this->assertSame($key, $request->getHeaderLine($header));
    }

    public function test_query_authenticator(): void
    {
        $param = 'api_key';
        $key = 'key';

        $request = $this->authenticateRequest(new QueryAuthenticator($param, $key));

        $this->assertNotEmpty($query = $request->getUri()->getQuery());
        $this->assertStringContainsString(sprintf('%s=%s', $param, $key), $query);
    }
}
