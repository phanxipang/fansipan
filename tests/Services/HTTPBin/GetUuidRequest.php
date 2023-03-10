<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Request;

final class GetUuidRequest extends Request
{
    public function endpoint(): string
    {
        return '/uuid';
    }
}
