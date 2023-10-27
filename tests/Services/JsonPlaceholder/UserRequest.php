<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\JsonPlaceholder;

use Fansipan\ConnectorlessRequest;

final class UserRequest extends ConnectorlessRequest
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function endpoint(): string
    {
        return 'https://jsonplaceholder.typicode.com/users/'.$this->id;
    }
}
