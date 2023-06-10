<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Body\AsJson;
use Jenky\Atlas\Request;

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
