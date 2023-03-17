---
order: 500
---

From the [Quick start](../getting-started/quickstart.md#create-request), you can send the request immediately without a HTTP client. However in real world use cases, typically you need to customize your HTTP client with options such as custom authorization header, timeout, etc...

## Writing Connectors

All connectors extends `Jenky\Atlas\Connector` class.

### Use Custom HTTP Client

Within the `defineClient` method, you should create your HTTP own client

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

Sometimes, as an end user, you can't use `defineClient` method. However you can override the client by using `withClient` method

```php
$connector = (new MyConnector())->withClient(
    new Client()
);
```

Then you can start [sending your request](requests.md#making-requests).

```php
$connector = new GithubConnector(token: 'github-token');

$connector->send(new GetUserRepoRequest('jenky/atlas');
```

This allows you to have constructor arguments on the connector, perfect for API tokens or configuration and also utilizes the powerful [middleware pipeline](../advanced/middleware.md) feature.

In addition, you can also bind your connector to [PSR-11](https://www.php-fig.org/psr/psr-11/) container so you don't have create a connector every time you want to send requests.

## Advanced Usage

Please visit the `Advanced` chapter to explore additional usage of connector.
