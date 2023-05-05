---
order: 400
---

Requests are classes that store all the information required to make a request. Within a request, you can define the HTTP Method (GET, POST, etc.) and the endpoint that you would like to make a request. You can also define default headers and query parameters. Traditionally, you would write your HTTP requests each time you need to, but this way, you can write a request class once and use it multiple times in your application.

## Making Requests

Your request should extend the `Jenky\Atlas\Request` abstract class. After that, you should define the endpoint of the request by using `endpoint` method. In addition you can also set the HTTP method by using `$method` property.

```php
<?php

final class MyRequest extends Request
{
    protected $method = 'POST';

    public function endpoint(): string
    {
        return 'https://httpbin.org/anything';
    }
}
```

!!!
Requests are mutable.
!!!

## Default Headers And Query Parameters

Some requests require specific headers or query parameters to be sent. To define default headers on your request, you can extend the `defaultHeaders` method.  For query parameters you can use `defaultQuery` method. These methods expect a keyed array to be returned.

```php
<?php

use Jenky\Atlas\Request;

final class MyRequest extends Request
{
    protected $method = 'POST';

    public function endpoint(): string
    {
        return '/anything';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'X-Foo' => 'baz',
        ];
    }

    protected function defaultQuery(): array
    {
        return [
            'limit' => 50,
        ];
    }
}
```

## Sending Data

Most API integrations will often require sending data using a `POST`,`PUT` or `PATCH` request. You can use `defaultBody` method to do so. By default, data will be sent using the `application/x-www-form-urlencoded` content type.

```php
<?php

use Jenky\Atlas\Request;

final class MyRequest extends Request
{
    protected $method = 'POST';

    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function endpoint(): string
    {
        return '/anything';
    }

    protected function defaultBody()
    {
        return [
            'type' => $this->type,
            'ping' => 'pong'
        ];
    }
}
```

### JSON requests

If you would like to send data using the `application/json` content type, you should add `AsJson` trait to your request:

```php
<?php

use Jenky\Atlas\Body\AsJson;
use Jenky\Atlas\Request;

final class MyRequest extends Request
{
    use AsJson;

    // ...
}
```

### Multi-part requests

If you would like to send files as multi-part requests, you should create your file using `Jenky\Atlas\Body\MultipartResource::from()` static method. This method accepts:
- `resource` stream.
- `string` the file absolute path in the system.
- `Psr\Http\Message\UploadedFileInterface` instance.
- [`SplFileObject`](https://www.php.net/manual/en/class.splfileobject.php) instance.

```php
<?php

use Jenky\Atlas\Body\AsMultipart;
use Jenky\Atlas\Body\Multipart;
use Jenky\Atlas\Request;

final class MyRequest extends Request
{
    use AsMultipart;

    // ...

    protected function defaultBody()
    {
        return [
            'hero_name' => 'Superman',
            'name' => 'Clark Kent',
            'avatar' => MultipartResource::from(__DIR__.'/../path_to_image'),
        ];
    }
}
```

!!!
You can create your own multipart file to fit your application logic. It must implement the `Jenky\Atlas\Contracts\MultipartInterface`.
!!!

### Raw requests

You may use the `AsText` trait if you would like to provide a raw request body when making a request:

```php
<?php

use Jenky\Atlas\Body\AsText;
use Jenky\Atlas\Request;

final class MyRequest extends Request
{
    use AsText;

    public function defaultBody()
    {
        return base64_encode($photo);
    }
}
```

!!!
Unlike `AsJson` or `AsMultipart` that set the content type automatically. When sending a raw request body, the content type header must be set manually.
!!!

!!!danger
Do not use multiple `As...` traits in your request.
!!!

## Modifying Request

Requests headers, query parameters and body can also be overwritten during runtime.

+++ Headers
```php
$request = new MyRequest();

// Add new headers
$request->headers()
    ->with('Content-Type', 'application/json')
    ->merge([
        'X-Custom' => 1,
        'X-Key' => 'key',
    ]);

// Overwrite existing headers
$request->headers()->set([
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
]);
```
+++ Query parameters
```php
$request = new MyRequest();

// Add new query parameters
$request->query()
    ->with('page', 1)
    ->merge([
        'limit' => 30,
        'search' => 'keyword',
    ]);

// Overwrite existing query parameters
$request->query()->set([
    'page' => 2,
    'limit' => 100
]);
```
+++ Body
```php
$request = new MyRequest();

// Add new body
$request->body()
    ->with('name', 'David')
    ->merge([
        'email' => 'david@example.com',
        'homepage' => 'https://example.com',
    ]);

// Overwrite existing body
$request->body()->set([
    'name' => 'Daisy',
    'email' => 'daisy@example.com',
]);
```
+++

## Using Constructor Arguments

You will often have variables that you want to pass into the request. You may add your own properties to your request class or use a constructor to provide variables into the request instance. Since the request is still a regular class you may customise it how you like.

For example, I want to create a request to update an individual user by an ID. I will add a constructor to accept the user ID and I will concatenate the variable with the endpoint. This way I can pass the ID into every instance of the request.

```php
<?php

use Jenky\Atlas\Request;
use Psr\Http\Client\ClientInterface;

final class UpdateUserRequest extends Request
{
    protected $method = 'PUT';

    private $id;

    private $data = [];

    public function __construct(int $id, array $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function endpoint(): string
    {
        return '/users/'.$this->id;
    }

    protected function defaultBody()
    {
        return $this->data;
    }
}

//

$request = new UpdateUserRequest(123, [
    'name' => 'John Doe',
    'age' => 25,
]);
```

## Sending Requests

Once you have the request instance, you can send it via connector like this:

```php
$connector = new MyConnector();
$request = new MyRequest();

$request->query()
    ->with('page', 2);

$response = $connector->send($request);
```

### Sending Request without Connector

While the typical setup of a connector and requests is great, sometimes all you need is to make a single request to a service. For scenarios like these, you may create a "`ConnectorlessRequest`" instead of making a connector and a single request. This saves you from having to create additional classes.

!!!danger
It is NOT recommended to send your requests without connector. Be aware of the [downsides](#downsides).
!!!

Create a request class, but instead of extending `Jenky\Atlas\Request`, you should extend `Jenky\Atlas\ConnectorlessRequest`. Next, just define everything else like you would a normal request. Make sure to include the full URL of the service you are integrating with.

```php
<?php

use Jenky\Atlas\ConnectorlessRequest;

final class GetUsersRequest extends ConnectorlessRequest
{
    public function endpoint(): string
    {
        return 'https://jsonplaceholder.typicode.com/users';
    }
}
```

As you don't have a connector for this request, you can use the `send` method directly on the request instance. This method works exactly the same as it would on the connector.

```php
$request = new GetUsersRequest();
$response = $request->send();
```

#### Downsides
- Not being able to have constructor arguments on your connector.
- Not retryable.
- Unable to add/remove middleware.
