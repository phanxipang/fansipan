---
label: Response Decoder
---

HTTP response is a crucial aspect of web development, and it is essential to decode the response body to extract the necessary information. However, the response body is typically returned in a string format as JSON or XML, which can be challenging to work with. This is where a response decoder comes in handy. A response decoder can convert the HTTP response body from a string format to a more manageable format, such as an array. This conversion enables developers to extract the relevant information from the response quickly.

## Configuring

The decoder should be configured as per-request basis. By default `Fansipan\Request` uses `ChainDecoder` to decode the response body. Essentially, it iterates over a list of `JsonDecoder` and `XmlDecoder` and attempts to read the `Content-Type` header to determine which one to use for decoding the body.

## Creating Custom Decoder

To create a custom decoder, you need to implement [`DecoderInterface`](https://github.com/phanxipang/fansipan/blob/main/src/Contracts/DecoderInterface.php) which defines the structure that a decoder must have. The contract contains only one method: `decode` where you can implement your own logic to decode the response body. Then you can start using it in your request.

```php
use Fansipan\Contracts\DecoderInterface;
use Fansipan\Request;

class MyRequest extends Request
{
    public function decoder(): DecoderInterface
    {
        return new MyCustomDecoder();
    }
}
```

## Mapping Response Body To Object

When dealing with the response body of an HTTP request, utilizing Data Transfer Objects (DTOs) or Value Objects (VOs) can greatly enhance the way data is represented, processed, and transferred.

- **Standardized Data Structure**: DTOs and VOs provide a standardized and consistent structure for representing the response body. This ensures that the data is organized in a predictable manner, making it easier for developers to understand and work with the response data. With a well-defined structure, it becomes simpler to parse, validate, and manipulate the response data.

- **Versioning and Compatibility**: DTOs and VOs can help manage versioning and compatibility issues when dealing with APIs. If the structure of the response body changes over time, having DTOs/VOs in place allows for seamless handling of different versions. New fields can be added or deprecated fields can be removed in a controlled manner, without affecting the consuming codebase. This decoupling between the API and the consuming code helps to maintain backward compatibility and avoids breaking changes.

- **Validation and Data Integrity**: DTOs and VOs can incorporate data validation rules, ensuring that the response body adheres to specific constraints or business rules. By validating the data at the point of parsing or deserialization into DTOs/VOs, potential errors or inconsistencies can be detected early on. This helps to maintain data integrity and prevents the propagation of invalid or inconsistent data throughout the application.

- **Serialization and Deserialization**: DTOs and VOs can simplify the process of serializing and deserializing the response data. Many frameworks and libraries provide built-in mechanisms for converting objects to and from various formats such as JSON or XML. By utilizing DTOs/VOs, the serialization and deserialization process becomes more straightforward, reducing the amount of boilerplate code required.

- **Testability**: DTOs and VOs can greatly enhance the testability of the code that handles HTTP requests. By representing the response body with DTOs/VOs, it becomes easier to write unit tests that verify the behavior of the code in isolation. Mocking or stubbing the response data becomes simpler, as the expected structure is well-defined. This improves the overall test coverage and makes it easier to catch bugs and regressions.

### Creating Mapper

To create a mapper, your decoder must also implements [`MapperInterface`](https://github.com/phanxipang/fansipan/blob/main/src/Contracts/MapperInterface.php) and include additional logic in the `map` method to map the response body to an object.

+++ Decoder
```php
use Fansipan\Contracts\DecoderInterface;
use Fansipan\Contracts\MapperInterface;

final class MyUserMapperDecoder implements DecoderInterface, MapperInterface
{
    public function decode(ResponseInterface $response): iterable
    {
        return \json_decode((string) $response->getBody(), true);
    }

    public function map(ResponseInterface $response): ?object
    {
        // this is a very basic piece of code,
        // you should also handle the mapping in case the response fails.
        $decoded = $this->decode($response);

        return new User(
            $decoded['id'] ?? 0,
            $decoded['name'] ?? '',
        );
    }
}
```
+++ Request
```php
use Fansipan\Contracts\DecoderInterface;
use Fansipan\Request;

final class GetUserRequest extends Request
{
    public function decoder(): DecoderInterface
    {
        return new MyUserMapperDecoder();
    }
}
```
+++ User DTO
```php
final class User
{
    public int $id;

    public string $name;
}
```
+++

You can also utilize a (de)serializer/mapper in your decoder to automatically create the object. Here is an example using [`cuyz/valinor`](https://github.com/CuyZ/Valinor):

!!!
The flowing examples use PHP 8.1+ syntax.
!!!

```php
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Fansipan\Contracts\DecoderInterface;
use Fansipan\Contracts\MapperInterface;
use Fansipan\Decoder\ChainDecoder;
use Psr\Http\Message\ResponseInterface;

/**
 * @template T of object
 * @implements MapperInterface<T>
 */
final class CustomDecoder implements DecoderInterface, MapperInterface
{
    private TreeMapper $mapper;

    private DecoderInterface $decoder;

    /**
     * @param  string|class-string<T> $signature
     */
    public function __construct(
        private readonly string $signature,
        ?TreeMapper $mapper = null,
        ?DecoderInterface $decoder = null
    ) {
        $this->mapper = $mapper ?? (new MapperBuilder())
            ->allowSuperfluousKeys()
            ->mapper();

        $this->decoder = $decoder ?? ChainDecoder::default();
    }

    public function map(ResponseInterface $response): ?object
    {
        $status = $response->getStatusCode();
        $decoded = $this->decode($response);

        if ($status >= 200 && $status < 300) {
            return $this->mapper->map($this->signature, $decoded);
        } else {
            return $this->mapper->map(ErrorResponse::class, $decoded); // "Error" response object
        }
    }

    public function decode(ResponseInterface $response): iterable
    {
        return $this->decoder->decode($response);
    }
}
```

It is entirely up to the SDK developer to choose the (de)serializer/mapper to work with. Some notable mentions are:

- [`cuyz/valinor`](https://github.com/CuyZ/Valinor)
- [`symfony/serializer`](https://github.com/symfony/serializer)
- [`eventsauce/object-hydrator`](https://github.com/EventSaucePHP/ObjectHydrator)
- [`crell/serde`](https://github.com/Crell/Serde)
- [`spatie/laravel-data`](https://github.com/spatie/laravel-data)
- [`jms/serializer`](https://github.com/schmittjoh/serializer)
- [`netresearch/jsonmapper`](https://github.com/cweiske/jsonmapper)
- [`json-mapper/json-mapper`](https://github.com/JsonMapper/JsonMapper)
- [`brick/json-mapper`](https://github.com/brick/json-mapper)

### Using Your Object

Then, you can retrieve your response body as an object:

```php
$response = $connector->send(new GetUserRequest());

/** @var User $user */
$user = $response->object();
```

#### Typed object with generics support

Additionally, you can define the type your DTO/VO via generics annotations:

```php
use Fansipan\Request;

/**
 * @extends Request<User>
 */
final class GetUserRequest extends Request
{

}

$response = $connector->send(new GetUserRequest());

$user = $response->object();
// Your IDE recognizes that $user is an instance of User without the need for an @var annotation.
echo $user->id;
```

The `@extends` annotation allows your IDE to understand which type your response body, and therefore allows for better static code analysis and code completion.

### Working With List of Resources

Dealing with a list of resources is common when working with API endpoints, such as fetching a [list of users](https://jsonplaceholder.typicode.com/users). In typical scenarios, your code would have a method that returns something like this:

```php
/**
 * @return User[]
 */
public function fetchUsers(): array
{
    //
}
```

However the `$response->object()` method's return type is `?object`, which is not suitable. Fortunately, you can utilize a collection library like [`ramsey/collection`](https://github.com/ramsey/collection) or [Laravel Collection](https://laravel.com/docs/latest/collections) to represent your list of users as an object. If you prefer not to install additional dependencies, PHP's built-in [`ArrayIterator`](https://www.php.net/manual/en/class.arrayiterator.php) is a solid choice for handling the list of resources.
