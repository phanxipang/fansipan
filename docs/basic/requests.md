---
order: 400
---

Requests are classes that store all the information required to make a request. Within a request, you can define the connector, the HTTP Method (GET, POST, etc.) and the endpoint that you would like to make a request. You can also define headers and query parameters. Traditionally, you would write your HTTP requests each time you need to, but this way, you can write a request class once and use it multiple times in your application.

## Making Requests

Your request should extend the `Jenky\Atlas\Request` abstract class. After that, you should define the endpoint of the request by using `endpoint` method. In addition you can also set the HTTP method by using `$method` property.

```php
<?php

class MyRequest extends Request
{
    protected $method = 'POST';

    public function endpoint(): string
    {
        return 'https://httpbin.org/anything';
    }
}
```

## Specify The Connector

Because connector contains your HTTP client instance and the middleware logic. Instead of using default connector, you can use your own connector by using `$connector` property.

+++ MyRequest.php
```php
<?php

use Jenky\Atlas\Request;

class MyRequest extends Request
{
    protected $connector = MyConnector::class;

    protected $method = 'POST';

    public function endpoint(): string
    {
        return '/anything';
    }
}
```
+++ MyConnector.php
```php
<?php

use Jenky\Atlas\Connector;
use Jenky\Atlas\Request;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class MyConnector extends Connector
{
    public function defineClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://httpbin.org',
            'timeout' => 10,
        ]);
    }
}
```
+++

Or if your appliation logic needs to overwrite the default `MyConnector` on the fly, you can do so by calling `withConnector` method before sending the request.

```php
$request = new MyRequest();
$request->withConnector(OtherConnector::class)->send();
// or using connector instance so you can pass the constructor arguments
$request->withConnector(new OtherConnector())->send();
```

## Default Headers And Query Parameters

Some requests require specific headers or query parameters to be sent. To define default headers on your request, you can extend the `defaultHeaders` method.  For query parameters you can use `defaultQuery` method. These methods expect a keyed array to be returned.

```php
<?php

use Jenky\Atlas\Request;

class MyRequest extends Request
{
    protected $connector = MyConnector::class;

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

class MyRequest extends Request
{
    protected $connector = MyConnector::class;

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

class MyRequest extends Request
{
    use AsJson;

    // ...
}
```

### Multi-part requests

If you would like to send files as multi-part requests, you should create your file using `Jenky\Atlas\Body\Multipart` class. This class accept:
- `string` the file aboslute path in the system.
- `Psr\Http\Message\UploadedFileInterface` instance.
- `SplFileInfo` instance.

```php
<?php

use Jenky\Atlas\Body\AsMutipart;
use Jenky\Atlas\Body\Multipart;
use Jenky\Atlas\Request;

class MyRequest extends Request
{
    use AsMutipart;

    // ...

    protected function defaultBody()
    {
        return [
            'hero_name' => 'Supermane',
            'name' => 'Clark Kent',
            'avatar' => new Multipart(__DIR__.'/../path_to_image'),
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

class MyRequest extends Request
{
    use AsText;

    public function defaultBody()
    {
        return base64_encode($photo);
    }
}
```

!!!
Unlike `AsJson` or `AsMutipart` that set the content type automatically. When sending a raw request body, the content type header must be set manually.
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

class UpdateUserRequest extends Request
{
    protected $connector = UserServiceConnector::class;

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
