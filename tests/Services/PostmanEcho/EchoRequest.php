<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\PostmanEcho;

use Fansipan\Request;

final class EchoRequest extends Request
{
    /**
     * @var string
     */
    protected $method;

    public function __construct(string $method = 'get')
    {
        $this->method = $method;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function endpoint(): string
    {
        return mb_strtolower($this->method);
    }
}
