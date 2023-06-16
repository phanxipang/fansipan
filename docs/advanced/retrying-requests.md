---
label: Retrying Requests
---

Sometimes you may deal with APIs that fail frequently because of network issues or temporary server errors. You may use the `Jenky\Atlas\RetryableConnector` decorator to send a request and retry multiple times.

## Getting Started

To retry a failed request, you should wrap you connector inside `Jenky\Atlas\RetryableConnector`. The connector accepts the maximum number of times the request should be attempted, a retry strategy to decide if the request should be retried, and to define the waiting time between each retry.

```php
use Jenky\Atlas\RetryableConnector;

$connector = new RetryableConnector(new MyConnector());
$response = $connector->send(new MyRequest());

// or retries for 5 times

$connector = new RetryableConnector(new MyConnector(), 5);
$response = $connector->send(new MyRequest());
```

## Customising When a Retry Is Attempted

By default, failed requests are retried up to 3 times, with an exponential delay between retries (first retry = 1 second; second retry: 2 seconds, third retry: 4 seconds) and only for the following HTTP status codes: `423`, `425`, `429`, `502` and `503` when using any HTTP method and `500`, `504`, `507` and `510` when using an HTTP [idempotent method](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Idempotent_methods).

If needed, you may pass a third argument to the `Jenky\Atlas\RetryableConnector` instance. It is an instance of `Jenky\Atlas\Contracts\RetryStrategyInterface` that determines if the retries should actually be attempted. This will retries the failed requests with a delay of 1 second.

```php
use Jenky\Atlas\RetryableConnector;
use Jenky\Atlas\Retry\RetryCallback;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$connector = new RetryableConnector(
    new MyConnector(),
    3,
    RetryCallback::when(static function (RequestInterface $request, ResponseInterface $response) {
        return $response->getStatusCode() >= 500;
    })
)->send(new MyRequest());
```

### Customising Delay

You may also pass second and third arguments to the `RetryCallback::when()` method to customise the waiting time between each retry.

```php
use Jenky\Atlas\RetryableConnector;
use Jenky\Atlas\Retry\RetryCallback;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$connector = new RetryableConnector(
    new MyConnector(),
    3,
    RetryCallback::when(static function (RequestInterface $request, ResponseInterface $response) {
        // Your logic here
    }, delay: 1000, multiplier: 2.0)
)->send(new MyRequest());
```

In the example above, failed requests are retried up to 3 times, with an exponential delay between retries (first retry = 1 second; second retry: 2 seconds, third retry: 4 seconds).

Instead of using an interval delay or calculated exponential delay, you may easily configure "exponential" backoffs by using `withDelay()` method. In this example, the retry delay will be 1 second for the first retry, 3 seconds for the second retry, and 10 seconds for the third retry:

```php
use Jenky\Atlas\RetryableConnector;
use Jenky\Atlas\Retry\Backoff;
use Jenky\Atlas\Retry\RetryCallback;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$connector = new RetryableConnector(
    new MyConnector(),
    3,
    RetryCallback::when(static function (RequestInterface $request, ResponseInterface $response) {
        // Your logic here
    })->withDelay(new Backoff([1, 3, 10]))
)->send(new MyRequest());
```

## Disabling Throwing Exceptions

If a request fails, it will be attempted again - if it reaches the maximum number of errors, a `Jenky\Atlas\Exception\RequestRetryFailedException` will be thrown. If a request is successful at any point, it will return a `Jenky\Atlas\Response` instance.

If you would like to disable this behavior, you may provide a `throw` argument with a value of `false`. When disabled, the last response received by the client will be returned after all retries have been attempted:


```php
$connector = new RetryableConnector(new MyConnector(), 3, null, throw: false);
$response = $connector->send(new MyRequest());
```

## Retrying All Requests

Since middleware is mutable, adding new middleware means that all subsequent requests will also have it applied.

+++ Definition
```php
<?php

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Traits\ConnectorTrait;

final class MyConnector implements ConnectorInterface
{
    use ConnectorTrait;
}
```
+++ Usage
```php
use Jenky\Atlas\Middleware\RetryRequests;
use Jenky\Atlas\Retry\Delay;
use Jenky\Atlas\Retry\GenericRetryStrategy;

$connector = new MyConnector();

$connector->middleware()->unshift(new RetryRequests(
    new GenericRetryStrategy(new Delay(1000, 2.0)),
));
$response = $connector->send(new MyRequest());

// or always retries for 5 times

$connector->middleware()->unshift(new RetryRequests(
    new GenericRetryStrategy(new Delay(1000, 2.0)),
    5
));
$response = $connector->send(new MyRequest());
```
+++
