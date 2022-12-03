<?php

declare(strict_types=1);

namespace Jenky\Atlas\Contracts;

use Jenky\Atlas\Response;

interface PendingRequestInterface
{
    /**
     * Send the request.
     *
     * @return \Jenky\Atlas\Response
     */
    public function send(): Response;
}
