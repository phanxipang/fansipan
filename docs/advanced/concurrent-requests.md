---
label: Concurrency & Pools
---

When building Web applications, it’s very common to make HTTP multiple requests from your backend code to external APIs. However, a common mistake when needing to make multiple HTTP requests at the same time is making them sequentially and waiting for every response before issuing the following request.

Sending HTTP requests concurrently can help to improve the performance of your application. This is because it allows you to process multiple requests in parallel, which can help to reduce the overall processing time.

With concurrency, we can batch all requests and execute them "at the same time". Under the hood, it uses [PHP 8.1 Fibers](https://php.watch/versions/8.1/fibers) feature. Atlas's concurrency is powered by [Amp Parallel](https://github.com/amphp/parallel) or [React Async](https://github.com/reactphp/async) implementations behind the scenes.

!!!danger
Concurrency & Pools are only supported in **PHP 8.1** and above.
!!!

## Getting Started

### Configuration

In order to send concurrent requests, your connector must implements `Jenky\Atlas\Contracts\PoolableInterface` interface and optionally add `Jenky\Atlas\Traits\Pollable` trait to the connector to fullfil the contract interface.

### Installation

As an SDK developer, you can skip this step if you intend to support both legacy PHP 7.x and PHP 8.0. Otherwise, you can choose to install these packages as outlined in the following step. Alternatively, you can leave it up to the end-users to decide which package they would like to use.

To install the pool functionality in Atlas, you need to install these packages using Composer:

```bash
composer require jenky/atlas-pool amphp/parallel
# or
composer require jenky/atlas-pool react/async
```

## Sending Requests

The `pool` method accepts 2 types of requests:
- List of requests
- List of Closure or Invokable class returning an array of responses

```php
use Jenky\Atlas\Pool\Pool;

$pool = new Pool(new MyConnector());

$responses = $pool->send([
    fn () => $connector->send(new MyFirstRequest()),
    fn () => $connector->send(new MySecondRequest()),
    fn () => $connector->send(new MyThirdRequest()),
]);

// or using the short way

$responses = $pool->send([
    new MyFirstRequest(),
    new MySecondRequest(),
    new MyThirdRequest(),
]);
```

!!!
It is recommended to provide a list of Closure or Invokable class instances to have more control over how the requests are sent, such as [`retrying request`](./retrying-requests.md), perform other tasks when responses come back and lazily initiate those requests.
!!!

## Retrieving Responses

```php
$connector = new PoolableConnector();

$responses = $connector->pool([
    fn () => $connector->send(new MyFirstRequest()),
    fn () => $connector->send(new MySecondRequest()),
    fn () => $connector->send(new MyThirdRequest()),
])->send();

return $responses[0]->ok() &&
    $responses[1]->ok() &&
    $responses[2]->ok();
```

As you can see, each response instance can be accessed based on the order it was added to the pool. If you wish, you can name the requests using the key of the array, which allows you to access the corresponding responses by name:

```php
$connector = new PoolableConnector();

$responses = $connector->pool([
    'first' => fn () => $connector->send(new MyFirstRequest()),
    'second' => fn () => $connector->send(new MySecondRequest()),
    'third' => fn () => $connector->send(new MyThirdRequest()),
])->send();

return $responses['first']->ok() &&
    $responses['second']->ok() &&
    $responses['third']->ok();
```
