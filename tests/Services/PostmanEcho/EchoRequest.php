<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\PostmanEcho;

use Jenky\Atlas\Request;

final class EchoRequest extends Request
{
    /**
     * @var string
     */
    protected $method;

    public function __construct(string $method = 'get')
    {
        $this->method = mb_strtolower($method);
    }

    public function endpoint(): string
    {
        return $this->method;
    }
}
