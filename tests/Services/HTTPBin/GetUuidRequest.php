<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Request;

final class GetUuidRequest extends Request
{
    public function endpoint(): string
    {
        return '/uuid';
    }
}
