<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Body\AsMultipart;
use Fansipan\Request;

final class PostRequest extends Request
{
    use AsMultipart;

    private $name;

    private $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function method(): string
    {
        return 'POST';
    }

    public function endpoint(): string
    {
        return '/post';
    }

    protected function defaultBody()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
