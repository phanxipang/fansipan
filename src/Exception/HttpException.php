<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exception;

use Jenky\Atlas\Response;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class HttpException extends RuntimeException implements ClientExceptionInterface
{
    /**
     * The response instance.
     *
     * @var \Jenky\Atlas\Response
     */
    private $response;

    public function __construct(Response $response)
    {
        parent::__construct($this->prepareMessage($response), $response->status());

        $this->response = $response;
    }

    /**
     * Get the response.
     */
    public function response(): Response
    {
        return $this->response;
    }

    /**
     * Prepare the exception message.
     */
    protected function prepareMessage(Response $response): string
    {
        return sprintf('HTTP request returned status code %d %s: %s',
            $response->status(), $response->reason(), $response->body()
        );
    }
}
