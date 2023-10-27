<?php

declare(strict_types=1);

namespace Fansipan;

use Fansipan\Contracts\ConnectorInterface;
use Fansipan\Traits\ConnectorTrait;

final class GenericConnector implements ConnectorInterface
{
    use ConnectorTrait;
}
