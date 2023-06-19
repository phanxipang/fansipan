<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exception;

class NotDecodableException extends \LogicException
{
    public static function create(string $message = 'Unable to decode the response body.'): self
    {
        return new self($message);
    }
}
