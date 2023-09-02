<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use GuzzleHttp\Psr7\MimeType;
use Http\Discovery\Psr17FactoryDiscovery;
use Jenky\Atlas\Contracts\MultipartInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Mime\MimeTypes;

final class MultipartResource implements MultipartInterface
{
    /**
     * @var \Psr\Http\Message\StreamInterface
     */
    private $stream;

    /**
     * @var string|null
     */
    private $filename;

    /**
     * @var string|null
     */
    private $mimeType;

    public function __construct(StreamInterface $stream, ?string $filename = null, ?string $mimeType = null)
    {
        $this->stream = $stream;
        $this->filename = $filename;
        $this->mimeType = $mimeType;
    }

    public function stream(): StreamInterface
    {
        return $this->stream;
    }

    public function filename(): ?string
    {
        return $this->filename ?? $this->getFilenameFromStream($this->stream);
    }

    public function mimeType(): ?string
    {
        if ($this->mimeType) {
            return $this->mimeType;
        }

        $filename = $this->filename();

        if (! $filename) {
            return null;
        }

        if (\class_exists(MimeType::class)) {
            return MimeType::fromFilename($filename);
        }

        if (\class_exists(MimeTypes::class)) {
            return MimeTypes::getDefault()->guessMimeType($filename);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if (! $finfo) {
            return null;
        }

        return \finfo_file($finfo, $filename) ?: null;
    }

    /**
     * Get the filename form the stream.
     */
    private function getFilenameFromStream(StreamInterface $stream): ?string
    {
        $uri = $stream->getMetadata('uri');

        if ($uri && \is_string($uri) && \substr($uri, 0, 6) !== 'php://' && \substr($uri, 0, 7) !== 'data://') {
            return basename($uri);
        }

        return null;
    }

    /**
     * Create a new multipart resource.
     *
     * @param  mixed $content
     * @throws \UnexpectedValueException
     */
    public static function from($content, ?string $filename = null, ?string $mimeType = null): self
    {
        if ($content instanceof StreamInterface) {
            return new self($content, $filename, $mimeType);
        }

        if ($content instanceof UploadedFileInterface) {
            return new self(
                $content->getStream(),
                $filename ?? $content->getClientFilename(),
                $mimeType ?? $content->getClientMediaType()
            );
        }

        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        if ($content instanceof \SplFileInfo) {
            $stream = $streamFactory->createStream(\file_get_contents($content->getPathname()) ?: '');

            return new self(
                $stream,
                $filename ?? $content->getBasename(),
                $mimeType
            );
        }

        if (\is_resource($content)) {
            return new self(
                $streamFactory->createStreamFromResource($content),
                $filename,
                $mimeType
            );
        }

        if (\is_string($content)) {
            try {
                $stream = $streamFactory->createStreamFromFile($content);
            } catch (\Throwable $e) {
                $stream = $streamFactory->createStream($content);
            }
        } else {
            throw new \UnexpectedValueException('Resource is not valid');
        }

        return new self($stream, $filename, $mimeType);
    }
}
