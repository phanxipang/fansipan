---
order: 500
icon: plug
---

From the [Quick start](../getting-started/quickstart.md#creating-request) guide, you have learned how to send a request without defining an HTTP client. However in real world use cases, typically you need to customize your HTTP client with options such as custom authorization header, timeout, etc...

## Writing Connectors

Within your connector, you can create your own HTTP client, set up the pipeline to run middleware, and define default middleware.

### Use Custom HTTP Client

To create your own HTTP client, use the `defaultClient` method.

```php
<?php

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;
use Fansipan\Request;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

final class MyConnector implements ConnectorInterface
{
    use ConnectorTrait;

    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public static function baseUri(): ?string
    {
        return 'https://my-service.api';
    }

    public function defaultClient(): ClientInterface
    {
        return new Client([
            'timeout' => 10,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
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

$connector->send(new GetUserRepoRequest('phanxipang/fansipan');
```

This allows you to have constructor arguments on the connector, perfect for API tokens or configuration and also utilizes the powerful [middleware pipeline](../advanced/middleware.md) feature.

Furthermore, it is possible to bind your connector to a [PSR-11 container](https://www.php-fig.org/psr/psr-11/). By doing so, you can inject your connector into your service whenever you need to send requests.

## Advanced Usage

Please visit the `Digging Deeper` chapter to explore additional usage of connector.
