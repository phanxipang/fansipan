<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

class NetworkException extends \RuntimeException implements NetworkExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        string $message,
        RequestInterface $request,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->request = $request;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
