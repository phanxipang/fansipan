<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Traits\ConnectorTrait;

final class GenericConnector implements ConnectorInterface
{
    use ConnectorTrait;
}
