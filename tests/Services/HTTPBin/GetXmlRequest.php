<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Request;

class GetXmlRequest extends Request
{
    protected $connector = Connector::class;

    public function endpoint(): string
    {
        return '/xml';
    }
}
