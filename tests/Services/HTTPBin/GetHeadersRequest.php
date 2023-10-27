<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Request;

final class GetHeadersRequest extends Request
{
    public function endpoint(): string
    {
        return '/headers';
    }
}
