---
order: 300
---

All requests will return an instance of `Fansipan\Response`, which is a decorator of `Psr\Http\Message\ResponseInterface` that provides a variety of convenience methods to inspect the response:

```php
$response->body(): string;
$response->data(): array;
$response->status(): int;
$response->ok(): bool;
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
