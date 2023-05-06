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
     * Get the filename of the part.
     */
    public function filename(): ?string;

    /**
     * Get the MIME type of the part.
     */
    public function mimeType(): ?string;
}
