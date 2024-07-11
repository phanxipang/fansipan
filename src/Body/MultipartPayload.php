<?php

declare(strict_types=1);

namespace Fansipan\Body;

use Fansipan\Contracts\MultipartInterface;
use Fansipan\Contracts\PayloadInterface;
use Fansipan\Map;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\StreamInterface;

final class MultipartPayload extends Map implements PayloadInterface
{
    /**
     * @var string
     */
    private $boundary;

    public function __construct(array $parameters = [], ?string $boundary = null)
    {
        parent::__construct($parameters);

        $this->boundary = $boundary ?: \bin2hex(\random_bytes(20));
    }

    public function contentType(): ?string
    {
        return 'multipart/form-data; boundary='.$this->boundary;
    }

    /**
     * Gather all the parts.
     */
    private function parts(): iterable
    {
        foreach ($this->all() as $key => $value) {
            yield $this->part($key, $value);
        }
    }

    /**
     * Build a single part.
     *
     * @param  string|MultipartInterface|StreamInterface $value
     */
    private function part(string $name, $value): string
    {
        // Set a default content-disposition header
        $headers['Content-Disposition'] = \sprintf(
            'form-data; name="%s"', $name
        );

        if ($value instanceof MultipartInterface) {
            if ($filename = $value->filename()) {
                $headers['Content-Disposition'] .= \sprintf('; filename="%s"', \basename($filename));
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

    public function __toString()
    {
        $str = '';

        foreach ($this->parts() as $part) {
            $str .= '--'.$this->boundary."\r\n".\trim($part)."\r\n";
        }

        $str .= '--'.$this->boundary."--\r\n";

        return $str;
    }
}
