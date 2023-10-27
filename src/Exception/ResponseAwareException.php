<?php

declare(strict_types=1);

namespace Fansipan\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

abstract class ResponseAwareException extends \RuntimeException implements ClientExceptionInterface
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
     *
     * @codeCoverageIgnore
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
        return \sprintf('HTTP request returned status code %d %s',
            $response->getStatusCode(), $response->getReasonPhrase()
        );
    }
}
