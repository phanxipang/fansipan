<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Request;

final class GetXmlRequest extends Request
{
    protected $connector = Connector::class;

    public function endpoint(): string
    {
        return '/xml';
    }
}
