<?php

declare(strict_types=1);

namespace Jenky\Atlas\Traits;

use Jenky\Atlas\Middleware;
use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Psr\Http\Client\ClientInterface;

trait ConnectorDecoratorTrait
{
    /**
     * @var \Jenky\Atlas\Contracts\ConnectorInterface
     */
    private $connector;

    public function client(): ClientInterface
    {
        return $this->connector->client();
    }

    public function middleware(): Middleware
    {
        throw new \LogicException('This connector does not allow to use middleware.');
    }

    public function send(Request $request): Response
    {
        return $this->connector->send($request);
    }
}
