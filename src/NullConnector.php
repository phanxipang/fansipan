<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Traits\ConnectorTrait;

final class NullConnector implements ConnectorInterface
{
    use ConnectorTrait;
}
