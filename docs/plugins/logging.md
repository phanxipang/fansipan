---
icon: log
---

To record your outgoing requests and responses for debugging and historical purposes, you can use the Fansipan logger plugin that takes advantage of [PSR-3](https://www.php-fig.org/psr/psr-3) loggers.

## Installation

You may use Composer to install package

```bash
composer require fansipan/logger-plugin
```

## Usage

Once the plugin has been installed, you may use the [`Logger`](https://github.com/phanxipang/logger-plugin/blob/main/src/Logger.php) middleware to setup the plugin:

+++ Before connector instantiation
```php
use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Log\Logger;
use Fansipan\Traits\ConnectorTrait;

final class Connector implements ConnectorInterface
{
    use ConnectorTrait;

    protected function defaultMiddleware(): array
    {
        return [
            /** @var \Psr\Log\LoggerInterface $logger */
            new Logger($logger),
        ];
    }
}
```
+++ After connector instantiation
```php
use Fansipan\Log\Logger;

/** @var \Psr\Log\LoggerInterface $logger */
$connector->middleware()->push(new Logger($logger));
```
+++

### Using a Custom Error Message

You can customize the log message by passing a second argument to the `Logger` constructor. This argument accepts an implementation of [`MessageFormatter`](https://github.com/phanxipang/logger-plugin/blob/main/src/MessageFormatter.php):

```php
use Fansipan\Log\HttpMessageFormatter;
use Fansipan\Log\Logger;

/** @var \Psr\Log\LoggerInterface $logger */
$connector->middleware()->push(new Logger($logger, new HttpMessageFormatter()));
```

The built-in `HttpMessageFormatter` allows you to set any template you want. By default, it uses the `DEBUG` template, which contains placeholders that will be replaced with actual values.

```php
public const CLF = '{hostname} {req.header_User-Agent} - [{date_common_log}] "{method} {target} HTTP/{version}" {code} {res.header_Content-Length}';
public const DEBUG = ">>>>>>>>\n{request}\n<<<<<<<<\n{response}\n--------\n{error}";
public const DEBUG_JSON = ">>>>>>>>\n{request:json}\n<<<<<<<<\n{response:json}\n--------\n{error}";
public const SHORT = '[{ts}] "{method} {target} HTTP/{version}" {code}';
```

```php
use Fansipan\Log\HttpMessageFormatter;

new HttpMessageFormatter(HttpMessageFormatter::CLF);
// or using your own
new HttpMessageFormatter('{request:json} <-> {response:json}');
```

### Log Level

By default, successful requests and responses are recorded using `info` level, while client errors (4xx) are recorded at the `error` level. You can also pass a third argument to the `Logger` constructor to override this behavior.

```php
use Fansipan\Log\HttpMessageFormatter;
use Fansipan\Log\Logger;
use Psr\Log\LogLevel;

/** @var \Psr\Log\LoggerInterface $logger */
new Logger($logger, new HttpMessageFormatter(), [
    LogLevel::INFO => [200, 399],
    LogLevel::ERROR => [400, 499],
    LogLevel::CRITICAL => [500, 599],
]);
```

The array key must be the log level, and the value can be either an integer representing a specific HTTP status code or an array representing a range of status codes.
