<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exceptions;

use Jenky\Atlas\Request;
use Jenky\Atlas\Response;
use Jenky\Atlas\Retry\RetryContext;

/**
 * An exception that indicates the request is failed and should be retried again.
 *
 * @internal
 */
final class RetryException extends \RuntimeException
{
    /**
     * @var \Jenky\Atlas\Request
     */
    private $request;

    /**
     * @var \Jenky\Atlas\Response
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
        Request $request,
        Response $response,
        RetryContext $context,
        int $delayMs
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->context = $context;
        $this->delay = $delayMs;

        parent::__construct('Retrying Request');
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
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
