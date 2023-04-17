<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exceptions;

use Jenky\Atlas\Retry\RetryContext;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * An exception that indicates the request is failed and should be retried again.
 *
 * @internal
 */
final class RetryException extends \RuntimeException
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * @var \Jenky\Atlas\Retry\RetryContext
     */
    private $context;

    /**
     * @var int
     */
    private $delay;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        RetryContext $context,
        int $delayMs
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->context = $context;
        $this->delay = $delayMs;

        $this->context->attempting();

        parent::__construct('Retrying Request');
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }

    public function delay(): int
    {
        return $this->delay;
    }

    public function retryable(): bool
    {
        $stop = $this->context->maxRetries() < $this->context->attempts();

        if ($stop && $this->context->throwable()) {
            throw new RequestRetryFailedException(
                sprintf('Maximum %d retries reached.', $this->context->maxRetries()),
                0,
                $this
            );
        }

        return ! $stop;
    }
}
