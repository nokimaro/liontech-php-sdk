<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Middleware that can intercept and modify responses.
 * Useful for token refresh handling.
 */
final class ResponseMiddleware
{
    /**
     * @var callable(ResponseInterface): ResponseInterface
     */
    private $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        return ($this->handler)($response);
    }

    /**
     * Create a middleware that does nothing.
     */
    public static function noop(): self
    {
        return new self(static fn (ResponseInterface $response): ResponseInterface => $response);
    }
}
