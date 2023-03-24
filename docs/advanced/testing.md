When testing HTTP clients, it is often necessary to simulate specific scenarios such as returning a successful response, returning an error, or returning specific responses in a certain order. However, hitting an actual remote API is not a good idea for unit tests as they need to be predictable, easy to bootstrap, and fast. This is because it can make your test run slower, waste rate limiting and even result in getting blocked by remote API.

Thanks to the [PSR-18 Client](https://www.php-fig.org/psr/psr-18/) and Atlas being client agnostic, testing has become much easier. You can now simply replace the underlying client with a mock client, eliminating the need to hit an actual remote API. Atlas provides a dedicated mock client for testing purposes, and you can find the full document on [Atlas Mock Client](https://github.com/jenky/atlas-mock-client).

```php
$client = new MockClient(
    MockResponse::create(['foo' => 'bar'])
);

$connector = (new MyConnector())->withClient($client);

$response = $connector->send(new MyRequest());

$this->assertTrue($response->ok());
$this->assertSame('bar', $response->data()['foo'] ?? '');

$client->assertSentCount(1);
```
