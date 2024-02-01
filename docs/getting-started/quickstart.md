---
order: 80
icon: zap
---

In this quickstart, we'll guide you through the most important functionalities of the package and how to use them.

[!ref](./installation.md)

## Create a Connector

In order to send a request. You should create a `Connector` class that implements `Fansipan\Contracts\ConnectorInterface` interface and add `Fansipan\Traits\ConnectorTrait` trait to the connector to fullfil the contract interface.

Additionally, you can set the request base URI by utilizing the `baseUri` static method. If a relative URI is specified in the request `endpoint` method, the connector will merge the base URI with the relative URI, following the guidelines outlined in [RFC 3986, section 5.2](https://www.rfc-editor.org/rfc/rfc3986#section-5.2).

```php
<?php

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;

final class Connector implements ConnectorInterface
{
    use ConnectorTrait;

    public static function baseUri(): ?string
    {
        return 'https://mydomain.com/api';
    }
}
```

## Creating Request

Let's say you want to send a request to `https://httpbin.org/headers`. Create `GetHeadersRequest` class that extends the `Fansipan\Request` abstract class and set the uri in `endpoint` public method. That's all.

```php
<?php

use Fansipan\Request;

final class GetHeadersRequest extends Request
{
    public function endpoint(): string
    {
        return 'https://httpbin.org/headers';

        // If your connector has a defined base URI, then it can be
        return '/headers';
    }
}
```

## Sending Request

Now you should be able to send the request:

```php
$connector = new Connector();
$request = new GetHeadersRequest();
$response = $connector->send($request);
```

## Inspecting response

The request above will return an instance of `Fansipan\Response`, which provides a variety of methods that may be used to inspect the response:

```php
if ($response->failed()) {
    return;
}

$data = $response->data();

// Perform your application logic.
```

You may be wondering where to find the HTTP client or how to customize it. In order to learn more about this topic, be sure to explore the next chapter, which delves into [connectors](./../basic/connectors.md), [requests](./../basic/requests.md) and [responses](./../basic/responses.md).
