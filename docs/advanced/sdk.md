---
label: SDK
---

![](../static/sdk-hero.png)

## Creating SDK Connector

```php
<?php

use GuzzleHttp\Client;
use Jenky\Atlas\Connector;
use Psr\Http\Client\ClientInterface;

class Github extends Connector
{
    private $token;

    private $version;

    public function __construct(string $token, ?string $version = null)
    {
        $this->token = $token;
        $this->version = $version;
    }

    protected function defineClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://api.github.com',
            'headers' => array_filter([
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer '.trim($this->token),
                'X-GitHub-Api-Version' => $this->version,
            ]),
        ]);
    }
}
```

### Using Connector

Now that we have created the SDK class that extends the `Jenky\Atlas\Connector` class, all we need to do is instansiate it and provide the API authentication token.

```php
$github = new Github('access-token');

// Ready to make request
```

## Sending request

When you have created the request, all that developers would need to do is run it! You can use the `send` method to send a request straight away, or the `request` method to instantiate the request.

```php
<?php

use Jenky\Atlas\Request;

class GetRepository extends Request
{
    private $owner;

    private $repo;

    public function __construct(string $owner, string $repo)
    {
        $this->owner = $owner;
        $this->repo = $repo;
    }

    public function endpoint(): string
    {
        return sprintf('/repos/%s/%s', $this->owner, $this->repo);
    }
}
```

```php
$github = new Github('access-token');

// Send a request straight away.
$github->send(new GetRepository('jenky', 'atlas'));

// Or if you would like to do something with the request before sending it.
$request = $github->request(new GetRepository('jenky', 'atlas'));
$request->headers()->with('X-Foo', 'baz');
$request->send();
```

## Using Request Collection

Alternatively, you can define request classes and groups of requests on your connector class by using the `$requests` property to define requests. By using this method, you will have to register your API routes, but then developers can use methods to make API calls. To enable request collection for a connector, add the `Jenky\Atlas\Traits\HasRequestCollection` trait to the connector:

```php
<?php

use GuzzleHttp\Client;
use Jenky\Atlas\Connector;
use Jenky\Atlas\Traits\HasRequestCollection;
use Psr\Http\Client\ClientInterface;

class Github extends Connector
{
    use HasRequestCollection;

    protected $requests = [
        GetRepository::class,
    ];

    // ...
}
```

```php
$github = new Github('access-token');

$request = $github->getRepository('jenky', 'atlas'));
$response = $request->send();
```

The connector will create a "magic" method for the request based on its name in `camelCase`. For example, our registered `GetRepository` class will now have a method for it on the connector called `getRepository()`. When you call this method, `GetRepository` class will be instantiated.

### Customising The Request Methods

Sometimes you may want to use your own method names for requests on your connector. If you would like to do this, just add a key for the request to rename the method.

```php
<?php

use GuzzleHttp\Client;
use Jenky\Atlas\Connector;
use Psr\Http\Client\ClientInterface;

class Github extends Connector
{
    protected $requests = [
        'get_repo' => GetRepository::class,
    ];

    // ...
}
```

```php
$github = new Github('access-token');

$response = $github->get_repo('jenky', 'atlas'))->send();
```

### Groupping Requests

You can also have many requests in an SDK, each separated into their own groups. For example, a "repos" group and a "orgs" group containing different requests.

Your requests now will be nested in a key that represents group name which also a method to access that group from the connector.

```php
<?php

use GuzzleHttp\Client;
use Jenky\Atlas\Connector;
use Psr\Http\Client\ClientInterface;

class Github extends Connector
{
    protected $requests = [
        'repos' => [
            'get' => GetRepository::class,
            'create' => PostRepository::class,
        ],
        'orgs' => [
            'repos' => ListOrgRepositories::class,
        ],
        Octocat::class,
    ];

    // ...
}
```

```php
$github = new Github('access-token');

$request = $github->repos()->get('jenky', 'atlas')); // GetRepository

$request = $github->orgs()->repos('github')); // ListOrgRepositories
```
