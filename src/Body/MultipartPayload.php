<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use Http\Discovery\Psr17FactoryDiscovery;
use Jenky\Atlas\Contracts\MultipartInterface;
use Jenky\Atlas\Contracts\PayloadInterface;
use Jenky\Atlas\Map;
use Psr\Http\Message\StreamInterface;

final class MultipartPayload extends Map implements PayloadInterface
{
    /**
     * @var string
     */
    private $boundary;

    /**
     * Create new multipart payload instance.
     *
     * @param  array  $parameters
     * @param  null|string  $boundary
     * @return void
     */
    public function __construct(array $parameters = [], ?string $boundary = null)
    {
        parent::__construct($parameters);

        $this->boundary = $boundary ?: bin2hex(random_bytes(20));
    }

    /**
     * Get the header content type value.
     */
    public function contentType(): ?string
    {
        return 'multipart/form-data; boundary='.$this->boundary;
    }

    /**
     * Gather all the parts.
     */
    private function parts(): array
    {
        $parts = [];

        foreach ($this->all() as $key => $value) {
            $parts[] = $this->part($key, $value);
        }

        return $parts;
    }

    /**
     * Build a single part.
     *
     * @param  string|MultipartInterface|StreamInterface $value
     */
    private function part(string $name, $value): string
    {
        // Set a default content-disposition header
        $headers['Content-Disposition'] = sprintf(
            'form-data; name="%s"', $name
        );

        if ($value instanceof MultipartInterface) {
            if ($filename = $value->filename()) {
                $headers['Content-Disposition'] .= sprintf('; filename="%s"', basename($filename));
            }

            // Set a default Content-Type
            if ($type = $value->mimeType()) {
                $headers['Content-Type'] = $type;
            }

            $stream = $value->stream();
        } else {
            $stream = $value instanceof StreamInterface
                ? $value
                : Psr17FactoryDiscovery::findStreamFactory()->createStream($value);
        }

        // Set a default content-length header
        if ($length = $stream->getSize()) {
            $headers['Content-Length'] = (string) $length;
        }

        $str = '';

        foreach ($headers as $key => $value) {
            $str .= "{$key}: {$value}\r\n";
        }

        $str .= "\r\n".(string) $stream;

        return $str;
    }

    /**
     * Get the string representation of the payload.
     */
    public function __toString()
    {
        $str = '';

        foreach ($this->parts() as $part) {
            $str .= '--'.$this->boundary."\r\n".trim($part)."\r\n";
        }

        $str .= '--'.$this->boundary."--\r\n";

        return $str;
    }
}
