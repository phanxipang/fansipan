<?php

declare(strict_types=1);

namespace Fansipan;

use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;

abstract class ConnectorlessRequest extends Request
{
    /**
     * Send the request.
     */
    final public function send(?ClientInterface $client = null): Response
    {
        $client = $client ?: Psr18ClientDiscovery::find();

        $response = $client->sendRequest(Util::request($this));

        return new Response($response, $this->decoder());
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
