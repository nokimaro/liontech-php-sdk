<?php

declare(strict_types=1);

namespace LionTech\SDK\Exceptions;

class ResourceNotFoundException extends SdkException
{
    public function __construct(
        string $message = 'Resource not found',
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
