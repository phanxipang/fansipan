Middleware provide a convenient mechanism for inspecting and modifying HTTP requests before sending.

Additional middleware can be written to perform a variety of tasks. For example, a logging middleware might log all outgoing requests and responses.

## Defining Middleware

To create a new middleware, create a new class and put your logic inside `__invoke` method:

```php
<?php

use Closure;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class AttachContentTypeRequestHeader
{
    public function __invoke(Request $request, Closure $next): Response
    {
        if ($contentType = $request->body()->contentType()) {
            $request->headers()->with('Content-Type', $contentType);
        }

        return $next($request);
    }
}
```

In this middleware, we will add a content type header to the request depends on the body type.

Middleware also can be a `Closure`:

```php

use Closure;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

function attach_content_type(string $contentType): Closure {
    return function (Request $request, Closure $next): Response {
        $request->headers()->with('Content-Type', $contentType);

        return $next($request);
    };
}
```

!!!danger
You must call the `$next` callback with the `$request` to pass the request deeper into the middleware pipeline.
!!!

### Middleware & Responses

Of course, a middleware can perform tasks before or after sending the request. For example, the following middleware would perform some task **before** the request is sent by the client:

```php
<?php

use Closure;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class BeforeMiddleware
{
    public function __invoke(Request $request, Closure $next): Response
    {
        // Perform action

        return $next($request);
    }
}
```

However, this middleware would perform its task **after** the request is sent:

```php
<?php

use Closure;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class AfterMiddleware
{
    public function __invoke(Request $request, Closure $next): Response
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
<?php

use Jenky\Atlas\Connector as BaseConnector;

class Connector extends BaseConnector
{
    protected function defaultMiddleware(): array
    {
        return [
            Middleware\AddCustomHeader::class,
        ];
    }
}
```

### Adding & Removing Middleware

Once the middleware has been created, you may use the `push` method to assign middleware to the pipeline:

```php
$connector->middleware()->push(new AddHeader('X-Foo', 'baz'));
```

Creating a middleware that modifies a request is made much simpler using the `Jenky\Atlas\Middleware\Interceptor::request()` method. This middleware accepts a function that takes the request argument:

```php
use Jenky\Atlas\Middleware\Interceptor;
use Jenky\Atlas\Request;

$connector->middleware()->push(Interceptor::request(function (Request $request): void {
    $request->headers()->with('X-Foo', 'bar');
}));
```

Modifying a response is also much simpler using the `Jenky\Atlas\Middleware\Interceptor::response()` middleware:

```php
use Jenky\Atlas\Middleware\Interceptor;
use Jenky\Atlas\Response;

$connector->middleware()->push(Interceptor::response(function (Response $response): void {
    // Log the response using PSR-3 logger
    $logger->info('Response received: ', $response->data());
}));
```

You can give middleware a name, which allows you to add middleware before other named middleware, after other named middleware, or remove middleware by name.

```php
// Add a middleware with a name
$connector->middleware()->push(Interceptor::request(function (Request $request): void {
    $request->headers()->with('X-Foo', 'bar');
}), 'add_foo');

// Add a middleware before a named middleware (unshift before).
$connector->middleware()->before('add_foo', Interceptor::request(function (Request $request): void {
    $request->headers()->with('X-Baz', 'qux');
}), 'add_baz');

// Add a middleware after a named middleware (pushed after).
$connector->middleware()->before('add_baz', Interceptor::request(function (Request $request): void {
    $request->headers()->with('X-Lorem', 'Ipsum');
}));

// Remove a middleware by name
$connector->middleware()->remove('add_foo');
```
