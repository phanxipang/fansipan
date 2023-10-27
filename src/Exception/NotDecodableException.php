<?php

declare(strict_types=1);

namespace Fansipan\Exception;

use Psr\Http\Client\ClientExceptionInterface;

class NotDecodableException extends \LogicException implements ClientExceptionInterface
{
    public static function create(string $message = 'Unable to decode the response body.'): self
    {
        return new self($message);
    }
}
