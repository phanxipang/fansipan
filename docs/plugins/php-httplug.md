---
order: 1
icon: plug
label: PHP-HTTP
---

The [PHP-HTTP (formerly HTTPlug) plugin](https://docs.php-http.org/en/latest/plugins/index.html) system allows to wrap a Client with [`PluginClient`](https://github.com/php-http/client-common/blob/2.x/src/PluginClient.php) and add some processing logic prior to and/or after sending the actual request. Fansipan includes an adapter that lets you use the plugin within the [middleware](../advanced/middleware.md) system.

## Installation

You may use Composer to install package

```bash
composer require fansipan/http-plugin-adapter
```

If you want to use the cache or logger plugins, you'll need to install those as well:

```bash
composer require fansipan/http-plugin-adapter php-http/cache-plugin

# or

composer require fansipan/http-plugin-adapter php-http/logger-plugin
```

## Usage

Once the plugin has been installed, you may use the [`PluginAdapter`](https://github.com/phanxipang/http-plugin-adapter/blob/main/src/PluginAdapter.php) middleware to setup the plugin:

+++ Before connector instantiation
```php
use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;
use Fansipan\HttpPluginAdapter\PluginAdapter;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\Plugin\CookiePlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Message\CookieJar;

final class Connector implements ConnectorInterface
{
    use ConnectorTrait;

    protected function defaultMiddleware(): array
    {
        return [
            new PluginAdapter([
                /** @var \Psr\Cache\CacheItemPoolInterface $pool */
                /** @var \Psr\Http\Message\StreamFactoryInterface $streamFactory */
                new CachePlugin($pool, $streamFactory),
                new CookiePlugin(new CookieJar()),
                /** @var \Psr\Log\LoggerInterface $logger */
                new Logger($logger),
            ])
        ];
    }
}
```
+++ After connector instantiation
```php
use Fansipan\HttpPluginAdapter\PluginAdapter;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\Plugin\CookiePlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Message\CookieJar;

$connector->middleware()->push(new PluginAdapter([
    /** @var \Psr\Cache\CacheItemPoolInterface $pool */
    /** @var \Psr\Http\Message\StreamFactoryInterface $streamFactory */
    new CachePlugin($pool, $streamFactory),
    new CookiePlugin(new CookieJar()),
    /** @var \Psr\Log\LoggerInterface $logger */
    new Logger($logger),
]));
```
+++
