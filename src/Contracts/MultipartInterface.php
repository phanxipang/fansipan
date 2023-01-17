<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Psr\Http\Message\StreamInterface;

interface MultipartInterface
{
    /**
     * Get the stream representing the part.
     */
    public function stream(): StreamInterface;

    /**
     * Determine whether the part is file.
     */
    public function isFile(): bool;

    /**
     * Get the filename in case part is file.
     */
    public function filename(): ?string;

    /**
     * Get the MIME type of the part.
     */
    public function mimeType(): ?string;
}
