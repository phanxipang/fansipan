---
order: 300
---

All requests will return an instance of `Jenky\Atlas\Response`, which is a decorator of `Psr\Http\Message\ResponseInterface` that provides a variety of convenience methods to inspect the response:

```php
$response->body(): string;
$response->data($key = null, $default = null): array|mixed;
$response->status(): int;
$response->failed(): bool;
$response->successful(): bool;
$response->failed(): bool;
$response->header(string $header): ?string;
$response->headers(): array;
```

## Throwing Exceptions On Failures

If you have a response instance and would like to throw an exception if the response status code indicates a client or server error, you may use the `throw` or `throwIf` methods:

```php
// Throw an exception if a client or server error occurred...
$response->throw();

// Throw an exception if an error occurred and the given condition is true...
$response->throwIf($condition);
```

## Casting To Data Transfer Objects (DTOs)

You may wish to cast the data you receive in an API response to a data transfer object (DTO).

### Configuring

The DTO should be configured as per-request basis. Firstly, your request class should implement the `Jenky\Atlas\Contracts\DtoSerializable` interface.

Then defines a required `toDto` method, you can also use the `$response` to inspect your data.

+++ GetUserRequest.php
```php
<?php

use Jenky\Atlas\Contracts\DtoSerializable;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class GetUserRequest extends Request implements DtoSerializable
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function endpoint(): string
    {
        return 'https://jsonplaceholder.typicode.com/users/'.$this->id;
    }

    public function toDto(Response $response): object
    {
        return User::fromArray($response->data());
    }
}
```
+++ User.php
```php
<?php

class User
{
    public $id;

    public $email;

    public $name;

    public function __construct($id, $email, $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
    }

    public static function fromArray(array $data = [])
    {
        return new static(
            $data['id'] ?? null,
            $data['email'] ?? null,
            $data['name'] ?? null,
        );
    }
}
```
+++

### Retrieving your DTO

Finally, when you retrieve a successful response from the API, you can use the `dto` method on the response to get the fully constructed DTO.

```php
$request = new GetUserRequest(1);
$reponse = $request->send();

/** @var User $user */
$user = $response->dto();
```

!!!danger
The response will only casted to your DTO if it was successful.
!!!
