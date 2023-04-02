<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

use GuzzleHttp\Psr7\MimeType;
use Http\Discovery\Psr17FactoryDiscovery;
use Jenky\Atlas\Contracts\MultipartInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use SplFileInfo;
use Symfony\Component\Mime\MimeTypes;
use Throwable;
use UnexpectedValueException;

class Multipart implements MultipartInterface
{
    /**
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @var \Psr\Http\Message\StreamInterface
     */
    private $stream;

    /**
     * @var bool
     */
    private $isFile = false;

    /**
     * @var null|string
     */
    private $filename = null;

    /**
     * Create new multipart instance.
     *
     * @param  mixed  $part
     * @param  null|\Psr\Http\Message\StreamFactoryInterface  $streamFactory
     * @return void
     */
    public function __construct($part, ?StreamFactoryInterface $streamFactory = null)
    {
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
        $this->analyse($part);
    }

    /**
     * Analyse the part.
     *
     * @param  mixed  $part
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */
    protected function analyse($part): void
    {
        if ($part instanceof UploadedFileInterface) {
            $this->isFile = true;
            $this->filename = $part->getClientFilename();
            $this->stream = $part->getStream();
        } elseif ($part instanceof SplFileInfo) {
            $this->isFile = $part->isFile();
            $this->filename = $part->getBasename();
            $this->stream = $this->createStream(
                file_get_contents($part->getPathname())
            );
        } else {
            $stream = $part instanceof StreamInterface
                ? $part
                : $this->createStream($part);

            if ($filename = $this->getFilenameFromStream($stream)) {
                $this->isFile = true;
                $this->filename = $filename;
            }
            $this->stream = $stream;
        }
    }

    /**
     * Create a stream for given part.
     *
     * @param  mixed  $content
     * @throws \UnexpectedValueException
     */
    private function createStream($content): StreamInterface
    {
        if (is_resource($content)) {
            return $this->streamFactory->createStreamFromResource($content);
        }

        if (! is_string($content)) {
            throw new UnexpectedValueException('Invalid stream content.');
        }

        try {
            return $this->streamFactory->createStreamFromFile($content);
        } catch (Throwable $e) {
            return $this->streamFactory->createStream($content);
        }
    }

    /**
     * Get the filename form the stream.
     */
    private function getFilenameFromStream(StreamInterface $stream): ?string
    {
        $uri = $stream->getMetadata('uri');

        if ($uri && is_string($uri) && substr($uri, 0, 6) !== 'php://' && substr($uri, 0, 7) !== 'data://') {
            return $uri;
        }

        return null;
    }

    /**
     * Determine whether the part is file.
     */
    public function isFile(): bool
    {
        return $this->isFile;
    }

    /**
     * Get the filename in case part is file.
     */
    public function filename(): ?string
    {
        return $this->filename;
    }

    /**
     * Get the content type of the part.
     */
    public function mimeType(): ?string
    {
        $filename = $this->filename();

        if (! $filename) {
            return null;
        }

        if (class_exists(MimeType::class)) {
            return MimeType::fromFilename($filename);
        }

        if (class_exists(MimeTypes::class)) {
            return MimeTypes::getDefault()->guessMimeType($filename);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if (! $finfo) {
            return null;
        }

        return finfo_file($finfo, $filename) ?: null;
    }

    /**
     * Get the stream representing the part.
     */
    public function stream(): StreamInterface
    {
        return $this->stream;
    }
}
