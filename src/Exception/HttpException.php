<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class HttpException extends RuntimeException implements ClientExceptionInterface
{
    /**
     * The response instance.
     *
     * @var ResponseInterface
     */
    private $response;

    public function __construct(
        ResponseInterface $response,
        string $message = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message ?: $this->prepareMessage($response),
            $response->getStatusCode(),
            $previous
        );

        $this->response = $response;
    }

    /**
     * Get the response.
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Prepare the exception message.
     */
    protected function prepareMessage(ResponseInterface $response): string
    {
        return sprintf('HTTP request returned status code %d %s',
            $response->getStatusCode(), $response->getReasonPhrase()
        );
    }
}
