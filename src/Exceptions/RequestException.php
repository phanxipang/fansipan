<?php

declare(strict_types=1);

namespace Jenky\Atlas\Exceptions;

use Jenky\Atlas\Request;
use Jenky\Atlas\Response;

class RequestException extends \RuntimeException
{
    /**
     * @var \Jenky\Atlas\Request
     */
    private $request;

    /**
     * @var null|\Jenky\Atlas\Response
     */
    private $response;

    public function __construct(Request $request, ?Response $response = null, string $message = '')
    {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message);
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function hasResponse(): bool
    {
        return $this->response instanceof Response;
    }

    public function response(): ?Response
    {
        return $this->response;
    }
}
