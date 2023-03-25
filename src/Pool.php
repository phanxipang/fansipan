<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use function React\Async\async;
use function React\Async\parallel;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Psr\Http\Message\ResponseInterface;

final class Pool
{
    public function __construct(private readonly ConnectorInterface $connector)
    {
    }

    public function send(iterable $requests)
    {
        $responses = [];

        foreach ($requests as $request) {
            $responses[] = async(fn () => $this->connector->send($request));
            // assert($response instanceof ResponseInterface);
        }

        return parallel($responses);
    }
}
