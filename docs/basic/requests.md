---
order: 400
---

Requests are classes that store all the information required to make a request. Within a request, you can define the HTTP Method (GET, POST, etc.) and the endpoint that you would like to make a request. You can also define default headers and query parameters. Traditionally, you would write your HTTP requests each time you need to, but this way, you can write a request class once and use it multiple times in your application.

## Making Requests

Your request should extend the `Fansipan\Request` abstract class. After that, you should define the endpoint of the request by using `endpoint` method. In addition you can also set the HTTP method by using `method` method, which defaults to `GET`.

```php
<?php

final class MyRequest extends Request
{
    public function method(): string
    {
        return 'POST';
    }

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

use Fansipan\Request;

final class MyRequest extends Request
{
    public function method(): string
    {
        return 'DELETE';
    }

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

use Fansipan\Request;

final class MyRequest extends Request
{
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function method(): string
    {
        return 'POST';
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

use Fansipan\Body\AsJson;
use Fansipan\Request;

final class MyRequest extends Request
{
    use AsJson;

    // ...
}
```

### Multi-part requests

If you would like to send files as multi-part requests, you should create your file using `Fansipan\Body\MultipartResource::from()` static method. This method accepts:
- `resource` stream.
- `string` the file absolute path in the system.
- `Psr\Http\Message\UploadedFileInterface` instance.
- [`SplFileObject`](https://www.php.net/manual/en/class.splfileobject.php) instance.

```php
<?php

use Fansipan\Body\AsMultipart;
use Fansipan\Body\Multipart;
use Fansipan\Request;

final class MyRequest extends Request
{
    use AsMultipart;

    // ...

    protected function defaultBody()
    {
        return [
            'hero_name' => 'Superman',
            'name' => 'Clark Kent',
            'avatar' => MultipartResource::from(__DIR__.'/../path_to_image.png'),
        ];
    }
}
```

You can also pass the second parameter as the filename and third parameter as content type of the file.

```php
$request = new MyRequest();

$request->with('image', MultipartResource::from(__DIR__.'/../path_to_image.jpg', 'image.jpeg', 'image/jpeg'));
```

!!!
You can create your own multipart file to fit your application logic. It must implement the `Fansipan\Contracts\MultipartInterface`.
!!!

### Raw requests

You may use the `AsText` trait if you would like to provide a raw request body when making a request:

```php
<?php

use Fansipan\Body\AsText;
use Fansipan\Request;

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

## Using Constructor Arguments

You will often have variables that you want to pass into the request. You may add your own properties to your request class or use a constructor to provide variables into the request instance. Since the request is still a regular class you may customise it how you like.

Let's consider an example where you need to create a request to update a specific user based on their ID. To achieve this, you can enhance the request by adding a constructor that accepts the user ID as a parameter. By concatenating the ID variable with the endpoint, you can ensure that the ID is passed to every instance of the request. This approach allows for a more streamlined and reusable implementation.

```php
<?php

use Fansipan\Request;
use Psr\Http\Client\ClientInterface;

final class UpdateUserRequest extends Request
{
    private $id;

    private $data = [];

    public function __construct(int $id, array $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function method(): string
    {
        return 'PUT';
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

Another example is this endpoint, [`https://jsonplaceholder.typicode.com/todos`](https://jsonplaceholder.typicode.com/todos), which supports the `_page` and `_limit` query parameters. Therefore, you should include something like this:

!!!
The flowing examples use PHP 8.1+ syntax.
!!!

```php
use Fansipan\Body\AsJson;
use Fansipan\Request;

final class TodosRequest extends Request
{
    use AsJson;

    public function __construct(
        private readonly ?int $page = null,
        private readonly ?int $limit = null
    ) {
    }

    protected function defaultQuery(): array
    {
        return \array_filter([
            '_page' => $this->page,
            '_limit' => $this->limit,
        ]);
    }
}
```

Likewise, if your endpoint has too many query strings for filtering, sorting, paging, etc., you should consider grouping them in their own dedicated value object instead of bloating the constructor.

+++ Request
```php
use Fansipan\Body\AsJson;
use Fansipan\Request;

final class TodosRequest extends Request
{
    use AsJson;

    public function __construct(
        private readonly ?FilterQuery $filter = null,
        private readonly ?SortQuery $sort = null,
        private readonly ?PaginationQuery $pagination = null
    ) {
    }

    protected function defaultQuery(): array
    {
        return \array_filter(\array_merge([
            'filter' => $this->filter->toArray(),
            'sort' => $this->sort->toArray(),
        ], $this->pagination->toArray()));
    }
}
```
+++ FilterQuery
```php
final class FilterQuery
{
    public function __construct(
        private ?string $name = null,
        private ?string $email = null
    ) {
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function withEmail(string $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function toArray(): array
    {
        return \array_filter([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
```
+++ SortQuery
```php
final class SortQuery
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    public function __construct(
        private ?string $by = null,
        private string $direction = self::DESC
    ) {
    }

    public function withSort(string $by): self
    {
        $clone = clone $this;
        $clone->by = $by;

        return $clone;
    }

    public function withDirection(string $direction): self
    {
        $clone = clone $this;
        $clone->direction = $direction;

        return $clone;
    }

    public function toArray(): array
    {
        if (empty($this->by)) {
            return [];
        }

        return [
            'sort' => $this->by,
            'direction' => $this->direction,
        ];
    }
}
```
+++ PaginationQuery
```php
final class PaginationQuery
{
    public function __construct(
        private int $page = 1,
        private ?int $limit = null
    ) {
    }

    public function withPage(int $page): self
    {
        $clone = clone $this;
        $clone->page = $page;

        return $clone;
    }

    public function withLimit(int $limit): self
    {
        $clone = clone $this;
        $clone->limit = $limit;

        return $clone;
    }

    public function toArray(): array
    {
        return \array_filter([
            'page' => $this->page,
            'limit' => $this->limit,
        ]);
    }
}
```
+++

## Modifying Request

Requests headers, query parameters and body can also be overwritten during runtime. However it is **RECOMMENDED** to setup your request [using constructor arguments](#using-constructor-arguments) to avoid mutating the request object. This approach makes it easier for the user to know which parameters should be used for sending the request, rather than dealing with keys and values.

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

Create a request class, but instead of extending `Fansipan\Request`, you should extend `Fansipan\ConnectorlessRequest`. Next, just define everything else like you would a normal request. Make sure to include the full URL of the service you are integrating with.

```php
<?php

use Fansipan\ConnectorlessRequest;

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
