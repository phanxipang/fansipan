---
label: Response Decoder
---

HTTP response is a crucial aspect of web development, and it is essential to decode the response body to extract the necessary information. However, the response body is typically returned in a string format as JSON or XML, which can be challenging to work with. This is where a response decoder comes in handy. A response decoder can convert the HTTP response body from a string format to a more manageable format, such as an array. This conversion enables developers to extract the relevant information from the response quickly.

### Configuring

The decoder should be configured as per-request basis. By default `Jenky\Atlas\Request` uses [`Jenky\Atlas\Decoder\ChainDecoder`](https://github.com/jenky/atlas/blob/18f96c176bed75fa321df6a675146820760e295f/src/Request.php#L124-L130) to decode the response body. Essentially, it iterates over a list of `JsonDecoder` and `XmlDecoder` and attempts to read the `Content Type` header to determine which one to use for decoding the body.

### Creating Custom Decoder

To create a custom decoder, you need to implement [`DecoderInterface`](https://github.com/jenky/atlas/blob/main/src/Contracts/DecoderInterface.php) which defines the structure that a decoder must have. The contract contains two methods: `supports` and `decode` where you can implement your own logic to decode the response body. Then you can start using it in your request.

```php
use Jenky\Atlas\Contracts\DecoderInterface;
use Jenky\Atlas\Request;

class MyRequest extends Request
{
    public function decoder(): DecoderInterface
    {
        return new MyCustomDecoder();
    }
}
```
