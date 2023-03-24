<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\JsonPlaceholder;

use Jenky\Atlas\ConnectorlessRequest;

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
