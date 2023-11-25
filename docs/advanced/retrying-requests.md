---
label: Retrying Requests
---

Sometimes you may deal with APIs that fail frequently because of network issues or temporary server errors. Fansipan has a useful built-in feature that allows you to send a request and retry multiple times.

## Getting Started

To retry a failed request, you should use the `Fansipan\ConnectorConfigurator` to configure your connector for retrying the request. The `retry` method accepts the maximum number of times the request should be attempted, a retry strategy to decide if the request should be retried, and to define the waiting time between each retry.

```php
use Fansipan\ConnectorConfigurator;

$connector = new MyConnector();
$response = (new ConnectorConfigurator())
    ->retry()
    ->configure($connector)
    ->send(new MyRequest());

// or retries for 5 times

$response = (new ConnectorConfigurator())
    ->retry(5)
    ->configure($connector)
    ->send(new MyRequest());
```

## Customising When a Retry Is Attempted

By default, failed requests are retried up to 3 times, with an exponential delay between retries (first retry = 1 second; second retry: 2 seconds, third retry: 4 seconds) and only for the following HTTP status codes: `423`, `425`, `429`, `502` and `503` when using any HTTP method and `500`, `504`, `507` and `510` when using an HTTP [idempotent method](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Idempotent_methods).

If needed, you may pass a third argument to the `Fansipan\RetryableConnector` instance. It is an instance of `Fansipan\Contracts\RetryStrategyInterface` that determines if the retries should actually be attempted. This will retries the failed requests with a delay of 1 second.

```php
use Fansipan\ConnectorConfigurator;
use Fansipan\Retry\RetryCallback;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$response = (new ConnectorConfigurator())
    ->retry(5, RetryCallback::when(static function (RequestInterface $request, ResponseInterface $response) {
        return $response->getStatusCode() >= 500;
    }))
    ->configure(new MyConnector())
    ->send(new MyRequest());
```

### Customising Delay

You may also pass second and third arguments to the `RetryCallback::when()` method to customise the waiting time between each retry.

```php
use Fansipan\ConnectorConfigurator;
use Fansipan\Retry\RetryCallback;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$response = (new ConnectorConfigurator())
    ->retry(3, RetryCallback::when(static function (RequestInterface $request, ResponseInterface $response) {
        // Your logic here
    }, delay: 1000, multiplier: 2.0))
    ->configure(new MyConnector())
    ->send(new MyRequest());
```

In the example above, failed requests are retried up to 3 times, with an exponential delay between retries (first retry = 1 second; second retry: 2 seconds, third retry: 4 seconds).

Instead of using an interval delay or calculated exponential delay, you may easily configure "exponential" backoffs by using `withDelay()` method. In this example, the retry delay will be 1 second for the first retry, 3 seconds for the second retry, and 10 seconds for the third retry:

```php
use Fansipan\ConnectorConfigurator;
use Fansipan\Retry\Backoff;
use Fansipan\Retry\RetryCallback;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$response = (new ConnectorConfigurator())
    ->retry(3, RetryCallback::when(static function (RequestInterface $request, ResponseInterface $response) {
        // Your logic here
    })->withDelay(new Backoff([1, 3, 10]))
    ->configure(new MyConnector())
    ->send(new MyRequest());
```

## Disabling Throwing Exceptions

If a request fails, it will be attempted again - if it reaches the maximum number of errors, a `Fansipan\Exception\RequestRetryFailedException` will be thrown. If a request is successful at any point, it will return a `Fansipan\Response` instance.

If you would like to disable this behavior, you may provide a `throw` argument with a value of `false`. When disabled, the last response received by the client will be returned after all retries have been attempted:


```php
use Fansipan\ConnectorConfigurator;

$response = (new ConnectorConfigurator())
    ->retry(2, null, throw: false)
    ->configure(new MyConnector())
    ->send(new MyRequest());
```

## Retrying All Requests Globally

Since middleware is mutable, adding new middleware means that all subsequent requests will also have it applied.

+++ Definition
```php
<?php

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;

final class MyConnector implements ConnectorInterface
{
    use ConnectorTrait;
}
```
+++ Usage
```php
use Fansipan\Middleware\RetryRequests;
use Fansipan\Retry\Delay;
use Fansipan\Retry\GenericRetryStrategy;

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
