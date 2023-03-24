When testing HTTP clients, you often need to simulate specific scenarios like returning a successful response, returning an error, or returning specific responses in a certain order. Because unit tests need to be predictable, easy to bootstrap, and fast, hitting an actual remote API is a not a good idea. It make your test run slower, wasting the rate limiting and eventually might getting blocked by remote API.

Thanks to the [PSR-18 Client](https://www.php-fig.org/psr/psr-18/) and Atlas being client agnostic, testing is easy by just replacing the underlying client with a mock client. Atlas has a dedicated mock client for testing, you can read the full document at [Atlas Mock Client](https://github.com/jenky/atlas-mock-client).

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
