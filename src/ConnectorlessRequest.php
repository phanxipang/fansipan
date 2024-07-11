<?php

declare(strict_types=1);

namespace Fansipan;

use Fansipan\Contracts\ConnectorInterface;

abstract class ConnectorlessRequest extends Request
{
    /**
     * Send the request.
     */
    final public function send(?ConnectorInterface $connector = null): Response
    {
        $connector = $connector ?: new GenericConnector();

        return $connector->send($this);
    }

    /**
     * @param  string|\Stringable $endpoint
     */
    public static function create($endpoint, string $method = 'GET'): self
    {
        return new class ((string) $endpoint, $method) extends ConnectorlessRequest {
            /**
             * @var string
             */
            private $endpoint;

            /**
             * @var string
             */
            private $method;

            public function __construct(
                string $endpoint,
                string $method = 'GET'
            ) {
                $this->endpoint = $endpoint;
                $this->method = $method;
            }

            public function endpoint(): string
            {
                return $this->endpoint;
            }

            public function method(): string
            {
                return $this->method;
            }
        };
    }
}
