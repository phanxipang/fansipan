<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Psr\Http\Message\StreamInterface;

interface MultipartInterface
{
    /**
     * Get the stream representing the part.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function stream(): StreamInterface;

    /**
     * Determine whether the part is file.
     *
     * @return bool
     */
    public function isFile(): bool;

    /**
     * Get the filename in case part is file.
     *
     * @return null|string
     */
    public function filename(): ?string;

    /**
     * Get the MIME type of the part.
     *
     * @return null|string
     */
    public function mimeType(): ?string;
}
