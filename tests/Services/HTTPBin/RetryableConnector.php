<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests\Services\HTTPBin;

use Jenky\Atlas\Contracts\RetryableInterface;
use Jenky\Atlas\Traits\Retryable;

final class RetryableConnector extends Connector implements RetryableInterface
{
    use Retryable;
}
