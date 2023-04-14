---
order: 500
---

From the [Quick start](../getting-started/quickstart.md#creating-request) guide, you have learned how to send a request without defining an HTTP client. However in real world use cases, typically you need to customize your HTTP client with options such as custom authorization header, timeout, etc...

## Writing Connectors

All connectors extends the `Jenky\Atlas\Connector` abstract class which implements all the necessary methods to fulfill the `Jenky\Atlas\Contracts\ConnectorInterface` contract. Within this class, you can create your own HTTP client, set up the pipeline to run middleware, and define default middleware.

### Use Custom HTTP Client

Within the `defaultClient` method, you should create your HTTP own client

```php
<?php

use Jenky\Atlas\Connector;
use Jenky\Atlas\Request;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class MyConnector extends Connector
{
    public function defaultClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://httpbin.org',
            'timeout' => 10,
        ]);
    }
}
```

As an end-user, you can override the client by using `withClient` method

```php
$connector = (new MyConnector())->withClient(new Client());
```

Then you can start [sending your request](requests.md#making-requests).

```php
$connector = new GithubConnector(token: 'github-token');

$connector->send(new GetUserRepoRequest('jenky/atlas');
```

This allows you to have constructor arguments on the connector, perfect for API tokens or configuration and also utilizes the powerful [middleware pipeline](../advanced/middleware.md) feature.

Furthermore, it is possible to bind your connector to a [PSR-11 container](https://www.php-fig.org/psr/psr-11/). By doing so, you can inject your connector into your service whenever you need to send requests.

## Advanced Usage

Please visit the `Digging Deeper` chapter to explore additional usage of connector.
