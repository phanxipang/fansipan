<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use PHPUnit\Framework\Assert;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait AssertTrait
{
    /**
     * @var array<int, array>
     */
    private $recorded = [];

    /**
     * Record a request response pair.
     */
    private function record(RequestInterface $request, ResponseInterface $response): void
    {
        $this->recorded[] = [$request, $response];
    }

    /**
     * Get a collection of the request / response pairs matching the given truth test.
     */
    public function recorded(?callable $callback = null): array
    {
        if (! $callback) {
            return $this->recorded;
        }

        return array_filter($this->recorded, function ($record) use ($callback) {
            return (bool) $callback(...$record);
        });
    }

    /**
     * Determine wether request was sent.
     *
     * @param  string|callable $condition
     */
    protected function checkRequestWasSent($condition): bool
    {
        if (empty($this->recorded)) {
            return false;
        }

        $callback = is_callable($condition)
            ? $condition
            : function (RequestInterface $request, ResponseInterface $response) use ($condition): bool {
                return Uri::matches((string) $condition, (string) $request->getUri());
            };

        return count($this->recorded($callback)) > 0;
    }

    /**
     * @param  string|callable $condition
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function assertSent($condition, string $message = 'An expected request was not sent.'): void
    {
        Assert::assertTrue($this->checkRequestWasSent($condition), $message);
    }

    /**
     * @param  string|callable $condition
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function assertNotSent($condition, string $message = 'An unexpected request was sent.'): void
    {
        Assert::assertFalse($this->checkRequestWasSent($condition), $message);
    }

    public function assertNothingSent(string $message = ''): void
    {
        Assert::assertEmpty($this->recorded, $message);
    }

    public function assertSentCount(int $count, string $message = ''): void
    {
        Assert::assertCount($count, $this->recorded, $message);
    }
}
