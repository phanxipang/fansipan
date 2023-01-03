---
order: 80
---

In this quickstart, we'll guide you through the most important functionalities of the package and how to use them.

First, you should [install the package](./installation.md).

## Flow Diagram

```mermaid
graph TD
    A([Request created]) -->|"send()"| B(Pending Request created)
    B --> C{Has custom connector}
    C -->|Use custom connector| D(Connector created)
    C -->|Use default connector| D
    B --> E[Build request uri] -->|Create PRS-7 request| F(PRS-7 request created)
    D -->|"send()"| H[Gather middleware] --> M[Pipe request and response] --> I(PRS-18 client created)
    I --- F --> K(PSR-7 response created) --> L([Response created])

```

## Create Request

Let's say you want to send a request to `https://httpbin.org/headers`. Create `GetHeadersRequest` class that extends the `Jenky\Atlas\Request` abstract class and set the uri in `endpoint` public method. That's all.

```php
<?php

use Jenky\Atlas\Request;

class GetHeadersRequest extends Request
{
    public function endpoint(): string
    {
        return 'https://httpbin.org/headers';
    }
}
```

Now you should be able to send the request:

```php
$request = new GetHeadersRequest();
$response = $request->send();
```

## Inspect the response

The request above will return an instance of `Jenky\Atlas\Response`, which provides a variety of methods that may be used to inspect the response:

```php
if ($response->failed()) {
    return;
}

$data = $response->data();

// Perform your application logic.
```

You may wonder where is the client or how can I customize it? Be sure to explore the next chapter about [connectors](./../basic/connectors.md), [requests](./../basic/requests.md) and [responses](./../basic/responses.md) to find out more.
