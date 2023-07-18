<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exception;

use Jenky\Atlas\Response;

class HttpException extends ResponseAwareException
{
    /**
     * The response instance.
     *
     * @var Response
     */
    private $decoratedResponse;

    public function __construct(
        Response $response,
        string $message = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $response->getResponse(),
            $message,
            $previous
        );

        $this->decoratedResponse = $response;
    }

    /**
     * Get the decorated response.
     *
     * @codeCoverageIgnore
     */
    public function response(): Response
    {
        return $this->decoratedResponse;
    }
}
