<?php

declare(strict_types=1);

namespace LionTech\SDK\Exceptions;

class RateLimitException extends SdkException
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        int $code = 429,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
