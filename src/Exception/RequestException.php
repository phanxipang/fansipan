<?php

declare(strict_types=1);

namespace Fansipan\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestException extends \RuntimeException implements RequestExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var null|ResponseInterface
     */
    private $response;

    public function __construct(
        string $message,
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?\Throwable $previous = null
    ) {
        $code = $response ? $response->getStatusCode() : 0;
        parent::__construct($message, $code, $previous);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the response.
     *
     * @codeCoverageIgnore
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
