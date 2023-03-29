---
label: Retrying Requests
---

Sometimes you may deal with APIs that fail frequently because of network issues or temporary server errors. You may use the `retry` method to send a request and retry multiple times.

## Getting Started

In order to retry a failed requests, your connector must implements `Jenky\Atlas\Contracts\RetryableInterface` interface and optionally add `Jenky\Atlas\Traits\Retryable` trait to the connector to fullfil the contract interface. The `retry` method accepts the maximum number of times the request should be attempted and a retry strategy to decide if the request should be retried, and to define the waiting time between each retry.

+++ Definition
```php
<?php

use Jenky\Atlas\Connector;
use Jenky\Atlas\Contracts\RetryableInterface;
use Jenky\Atlas\Traits\Retryable;

class MyConnector extends Connector implements RetryableInterface
{
    use Retryable;
}
```
+++ Usage
```php
$connector = new MyConnector();

$response = $connector->retry()->send(new MyRequest());
// or retries for 5 times
$response = $connector->retry(5)->send(new MyRequest());
```
+++

## Customising When a Retry Is Attempted

By default, failed requests are retried up to 3 times, with an exponential delay between retries (first retry = 1 second; second retry: 2 seconds, third retry: 4 seconds) and only for the following HTTP status codes: `423`, `425`, `429`, `502` and `503` when using any HTTP method and `500`, `504`, `507` and `510` when using an HTTP [idempotent method](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Idempotent_methods).

If needed, you may pass a second argument to the `retry` method. The second argument is an instance of `Jenky\Atlas\Contracts\RetryStrategyInterface` that determines if the retries should actually be attempted. This will retries the failed requests with a delay of 1 second.

```php
use Jenky\Atlas\Retry\RetryCallback;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

$connector->retry(3, RetryCallback::when(function (Request $request, Response $response) {
    return $response->serverError();
}))->send(new MyRequest());
```

### Customising Delay

You may also pass second and third arguments to the `RetryCallback::when()` method to customise the waiting time between each retry.

```php
use Jenky\Atlas\Retry\RetryCallback;

$connector->retry(3, RetryCallback::when(function (Request $request, Response $response) {
    // Your logic here
}, delay: 1000, multiplier: 2.0))->send(new MyRequest());
```

In the example above, failed requests are retried up to 3 times, with an exponential delay between retries (first retry = 1 second; second retry: 2 seconds, third retry: 4 seconds).

Instead of using an interval delay or calculated exponential delay, you may easily configure "exponential" backoffs by using `withDelay()` method. In this example, the retry delay will be 1 second for the first retry, 3 seconds for the second retry, and 10 seconds for the third retry:

```php
use Jenky\Atlas\Retry\Backoff;
use Jenky\Atlas\Retry\RetryCallback;

$connector->retry(3, RetryCallback::when(function (Request $request, Response $response) {
    // Your logic here
})->withDelay(new Backoff([1, 3, 10])))->send(new MyRequest());
```

!!! Default retry strategy
As a SDK developer, you may set the default retry strategy by defining `defaultRetryStrategy()` method in the connector class.
!!!

## Disabling Throwing Exceptions

If a request fails, it will be attempted again - if it reaches the maximum number of errors, a `RequestRetryFailedException` will be thrown. If a request is successful at any point, it will return a `Response` instance.

If you would like to disable this behavior, you may provide a `throw` argument with a value of `false`. When disabled, the last response received by the client will be returned after all retries have been attempted:


```php
$connector->retry(3, null, throw: false)->send(new MyRequest());
```
