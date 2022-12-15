<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exceptions;

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
    public $response;

    /**
     * Create a new exception instance.
     *
     * @param  \Jenky\Atlas\Response  $response
     * @return void
     */
    public function __construct(Response $response)
    {
        parent::__construct($this->prepareMessage($response), $response->status());

        $this->response = $response;
    }

    /**
     * Prepare the exception message.
     *
     * @param  \Jenky\Atlas\Response  $response
     * @return string
     */
    protected function prepareMessage(Response $response): string
    {
        return sprintf('HTTP request returned status code %d %s: %s',
            $response->status(), $response->reason(), $response->body()
        );
    }
}
