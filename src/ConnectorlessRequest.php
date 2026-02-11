<?php

declare(strict_types=1);

namespace Fansipan;

use Fansipan\Body\FormPayload;
use Fansipan\Contracts\PayloadInterface;
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
    public static function create($endpoint, string $method = 'GET', ?PayloadInterface $payload = null): self
    {
        return new class ((string) $endpoint, $method, $payload ?? new FormPayload()) extends ConnectorlessRequest {
            /**
             * @var string
             */
            private $endpoint;

            /**
             * @var string
             */
            private $method;

            /**
             * @var PayloadInterface
             */
            private $payload;

            public function __construct(
                string $endpoint,
                string $method,
                PayloadInterface $payload
            ) {
                $this->endpoint = $endpoint;
                $this->method = $method;
                $this->payload = $payload;
            }

            public function endpoint(): string
            {
                return $this->endpoint;
            }

            public function method(): string
            {
                return $this->method;
            }

            /**
             * Create new request body payload.
             */
            protected function definePayload(): PayloadInterface
            {
                return $this->payload;
            }
        };
    }
}
