<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Contracts\PoolableInterface;
use Jenky\Atlas\Traits\Poolable;

final class PoolableConnector extends Connector implements PoolableInterface
{
    use Poolable;
}
