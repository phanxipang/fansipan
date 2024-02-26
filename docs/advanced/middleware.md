---
order: 500
icon: mirror
---

Middleware provide a convenient mechanism for inspecting and modifying HTTP requests before sending.

Additional middleware can be written to perform a variety of tasks. For example, a logging middleware might log all outgoing requests and responses.

!!!
Middleware is **mutable**. If you want to apply middleware to only one request, use `clone` to avoid mutating the connector middleware.
!!!

## Defining Middleware

To create a new middleware, create a new Invokable class and put your logic inside `__invoke` method:

```php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AttachContentTypeRequestHeader
{
    private $contentType;

    public function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        return $next($request->withHeader('Content-Type', $this->contentType));
    }
}
```

In this middleware, we will add a content type header to the request depends on the body type.

Middleware also can be a `Closure`:

```php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

function attach_content_type(string $contentType): Closure {
    return static function (RequestInterface $request, callable $next): ResponseInterface {
        return $next($request->withHeader('Content-Type', $contentType));
    };
}
```

!!!danger
You must call the `$next` callback with the `$request` to pass the request deeper into the middleware pipeline.
!!!

### Middleware & Responses

Of course, a middleware can perform tasks before or after sending the request. For example, the following middleware would perform some task **before** the request is sent by the client:

```php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class BeforeMiddleware
{
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        // Perform action

        return $next($request);
    }
}
```

However, this middleware would perform its task **after** the request is sent:

```php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AfterMiddleware
{
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $response = $next($request);

        // Perform action

        return $response;
    }
}
```

## Registering Middleware

### Default Middleware

To register default middleware, list the middleware class in the `defaultMiddleware` method of your connector class.

```php
use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;

final class Connector implements ConnectorInterface
{
    use ConnectorTrait;

    protected function defaultMiddleware(): array
    {
        return [
            new Middleware\AddCustomHeader(),
        ];
    }
}
```

### Adding & Removing Middleware

Once the middleware has been created, you may use the `push` method to assign middleware to the pipeline:

```php
$connector->middleware()->push(new AddHeader('X-Foo', 'baz'));
```

Adding middleware to the connector will apply to all requests. If you only want to apply it to the current request, use the `middleware` method of `ConnectorConfigurator` to create a cloned instance of your connector:

```php
use Fansipan\ConnectorConfigurator;

$response = (new ConnectorConfigurator())
    ->middleware(new AddHeader('X-Foo', 'baz'))
    ->configure($connector)
    ->send(new MyRequest());
```

Creating a middleware that modifies a request is made much simpler using the `Fansipan\Middleware\Interceptor::request()` method. This middleware accepts a function that takes the request argument:

```php
use Fansipan\Middleware\Interceptor;
use Psr\Http\Message\RequestInterface;

$connector->middleware()->push(Interceptor::request(static function (RequestInterface $request) {
    return $request->withHeader('X-Foo', 'bar');
}));
```

Modifying a response is also much simpler using the `Fansipan\Middleware\Interceptor::response()` middleware:

```php
use Fansipan\Middleware\Interceptor;
use Psr\Http\Message\ResponseInterface;

$connector->middleware()->push(Interceptor::response(static function (ResponseInterface $response) {
    return $response->withHeader('X-Foo', 'bar');
}));
```

You can give middleware a name, which allows you to add middleware before other named middleware, after other named middleware, or remove middleware by name.

```php
// Add a middleware with a name
$connector->middleware()->push(Interceptor::request(static function (RequestInterface $request) {
    return $request->withHeader('X-Foo', 'bar');
}), 'add_foo');

// Add a middleware before a named middleware (unshift before).
$connector->middleware()->before('add_foo', Interceptor::request(static function (RequestInterface $request) {
    return $request->withHeader('X-Baz', 'qux');
}), 'add_baz');

// Add a middleware after a named middleware (pushed after).
$connector->middleware()->before('add_baz', Interceptor::request(static function (RequestInterface $request) {
    return $request->withHeader('X-Lorem', 'Ipsum');
}));

// Remove a middleware by name
$connector->middleware()->remove('add_foo');
```
