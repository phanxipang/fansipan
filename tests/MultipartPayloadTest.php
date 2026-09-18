<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\Body\MultipartPayload;
use Fansipan\Body\MultipartResource;
use Http\Discovery\Psr17FactoryDiscovery;

final class MultipartPayloadTest extends TestCase
{
    public function test_escapes_quotes_and_line_breaks_in_field_name(): void
    {
        $payload = new MultipartPayload(["a\"b\r\nc" => 'v'], 'B');

        $this->assertSame(
            "--B\r\n"
            ."Content-Disposition: form-data; name=\"a%22b%0D%0Ac\"\r\n"
            ."\r\n"
            ."v\r\n"
            ."--B--\r\n",
            (string) $payload
        );
    }

    public function test_escapes_quotes_and_line_breaks_in_filename(): void
    {
        $stream = Psr17FactoryDiscovery::findStreamFactory()->createStream('data');
        $file = new MultipartResource($stream, "x\"\r\ny.txt", 'text/plain');

        $payload = new MultipartPayload(['file' => $file], 'B');

        $this->assertSame(
            "--B\r\n"
            ."Content-Disposition: form-data; name=\"file\"; filename=\"x%22%0D%0Ay.txt\"\r\n"
            ."Content-Type: text/plain\r\n"
            ."\r\n"
            ."data\r\n"
            ."--B--\r\n",
            (string) $payload
        );
    }

    public function test_null_value_is_sent_as_empty_string(): void
    {
        $payload = new MultipartPayload(['note' => null], 'B');

        $this->assertSame(
            "--B\r\n"
            ."Content-Disposition: form-data; name=\"note\"\r\n"
            ."\r\n"
            ."\r\n"
            ."--B--\r\n",
            (string) $payload
        );
    }
}
