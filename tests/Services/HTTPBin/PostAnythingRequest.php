<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\HTTPBin;

use Fansipan\Body\AsJson;
use Fansipan\Request;

final class PostAnythingRequest extends Request
{
    use AsJson;

    public function method(): string
    {
        return 'POST';
    }

    public function endpoint(): string
    {
        return '/anything';
    }
}
