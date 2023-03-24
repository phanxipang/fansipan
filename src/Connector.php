<?php

declare(strict_types=1);

namespace Jenky\Atlas;

use Jenky\Atlas\Contracts\ConnectorInterface;
use Jenky\Atlas\Contracts\RetryableInterface;
use Jenky\Atlas\Exceptions\RetryException;

abstract class Connector implements ConnectorInterface
{
    use Traits\HasClient;
    use Traits\HasMiddleware;

    public function baseUri(): ?string
    {
        return null;
    }

    /**
     * Create a new pending request.
     */
    protected function request(Request $request): PendingRequest
    {
        return new PendingRequest($this, $request);
    }

    public function send(Request $request): Response
    {
        if ($this instanceof RetryableInterface) {
            return $this->sendAndRetry($request);
        }

        return $this->request($request)->send();
    }

    /**
     * Send the request and retries if it fails.
     *
     * @throws \Jenky\Atlas\Exceptions\RequestRetryFailedException
     */
    private function sendAndRetry(Request $request): Response
    {
        /* beginning:

        try {
            return $this->request($request)->send();
        } catch (RetryException $e) {
            if (! $e->retryable()) {
                return $e->response();
            }

            $delay = $e->delay();

            if ($delay > 0) {
                usleep($delay * 1000);
            }

            goto beginning;
        } */

        do {
            try {
                return $this->request($request)->send();
            } catch (RetryException $e) {
                if (! $e->retryable()) {
                    return $e->response();
                }

                $delay = $e->delay();

                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
        } while (true);
    }
}
