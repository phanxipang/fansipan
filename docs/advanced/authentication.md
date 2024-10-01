---
icon: lock
---

The Authenticator is a dedicated component designed to centralizes and simplifies the logic needed to manage client authentication.

## Using Authenticator

To register an global authenticator, [add the `Authentication` middleware](middleware/#registering-middleware) to the connector:

```php
use Fansipan\Authenticator\BasicAuthenticator;
use Fansipan\Authenticator\BasicAuthenticator;

protected function defaultMiddleware(): array
{
    return [
        new Authentication(new BasicAuthenticator('user', 'password')),
    ];
}
```

It also supports per-request authentication by using the `ConnectorConfigurator`:

```php
$response = (new ConnectorConfigurator())
    ->auth(new BasicAuthenticator('user', 'password'))
    ->configure($connector)
    ->send(new MyRequest());
```

## Built-in Authenticators

### Bearer Authenticator

The `BearerAuthenticator` class can be used to add a `Authorization: Bearer` header to the request

```php
use Fansipan\Authenticator\BearerAuthenticator;
use Fansipan\Authenticator\HeaderAuthenticator;

protected function defaultMiddleware(): array
{
    return [
        new Authentication(new BearerAuthenticator('token')),
    ];
}
```

### Basic Authenticator

The `BasicAuthenticator` class can be used to add a `Authorization: Basic` header to the request

```php
use Fansipan\Authenticator\QueryAuthenticator;

protected function defaultMiddleware(): array
{
    return [
        new Authentication(new BasicAuthenticator('user', 'password')),
    ];
}
```

### Header Authenticator

The `HeaderAuthenticator` class can be used to authenticate with a custom header

```php
use Fansipan\Middleware\Authentication;
use Fansipan\Middleware\Authentication;

protected function defaultMiddleware(): array
{
    return [
        new Authentication(new HeaderAuthenticator('X-Secret-Key', 'secret')),
    ];
}
```

### Query Authenticator

The `QueryAuthenticator` class can be used to add a query parameter to the request

```php
use Fansipan\Middleware\Authentication;
use Fansipan\Middleware\Authentication;

protected function defaultMiddleware(): array
{
    return [
        new Authentication(new QueryAuthenticator('api_key', 'key')),
    ];
}
```

## Custom Authenticator

If your integration requires a more complex authentication process, you can create your own authenticator. Authenticators are classes that must implement the [AuthenticatorInterface](https://github.com/phanxipang/fansipan/blob/main/src/Contracts/AuthenticatorInterface.php). This interface defines a single method called `authenticate`, which is used to manipulate the request before it is sent.

```php
use Fansipan\ConnectorlessRequest;
use Fansipan\Middleware\Authentication;

class CustomAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private string $user,
        private string $password,
    ) {
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $bearerAuthenticator = new BearerAuthenticator($this->getToken());

        return $bearerAuthenticator->authenticate($request);
    }

    private function getToken(): string
    {
        // Send a login request to retrieve the access token
        $request = ConnectorlessRequest::create('https://dummyjson.com/auth/login', 'POST');
        $request->body()->with('username', $this->user);
        $request->body()->with('password', $this->password);

        $response = $request->send();

        return $response->data()['accessToken'] ?? '';
    }
}
```
