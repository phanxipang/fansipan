<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\Body\MultipartResource;
use Http\Discovery\Psr17FactoryDiscovery;

final class MultipartTest extends TestCase
{
    public function test_create_multipart_from_file_path_with_name_and_mime(): void
    {
        $part = MultipartResource::from(__DIR__.'/fixtures/1x1.png', '1.png', 'image/png');

        $this->assertSame('1.png', $part->filename());
        $this->assertSame('image/png', $part->mimeType());
    }

    public function test_create_multipart_from_file_path(): void
    {
        $part = MultipartResource::from(__DIR__.'/fixtures/1x1.png');

        $this->assertSame('1x1.png', $part->filename());
        $this->assertSame('image/png', $part->mimeType());
    }

    public function test_create_multipart_from_resource(): void
    {
        $part = MultipartResource::from(fopen(__DIR__.'/fixtures/1x1.png', 'r'));

        $this->assertSame('1x1.png', $part->filename());
        $this->assertSame('image/png', $part->mimeType());
    }

    public function test_create_multipart_from_uploaded_file(): void
    {
        $stream = Psr17FactoryDiscovery::findStreamFactory()
            ->createStreamFromFile(__DIR__.'/fixtures/1x1.png');
        $file = Psr17FactoryDiscovery::findUploadedFileFactory()
            ->createUploadedFile($stream);

        $part = MultipartResource::from($file);

        $this->assertSame('1x1.png', $part->filename());
        $this->assertSame('image/png', $part->mimeType());
    }

    public function test_create_multipart_from_spl_file(): void
    {
        $file = new \SplFileObject(__DIR__.'/fixtures/1x1.png');

        $part = MultipartResource::from($file);

        $this->assertSame('1x1.png', $part->filename());
        $this->assertSame('image/png', $part->mimeType());
    }

    public function test_create_multipart_from_string(): void
    {
        $part = MultipartResource::from('foo');

        $this->assertNull($part->filename());
        $this->assertNull($part->mimeType());
    }
}
