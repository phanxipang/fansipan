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

To register a middleware, list the middleware class in the `defaultMiddleware` method of your connector class.

```php
<?php

use Jenky\Atlas\Connector as BaseConnector;
use Psr\Http\Client\ClientInterface;

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

WIP
