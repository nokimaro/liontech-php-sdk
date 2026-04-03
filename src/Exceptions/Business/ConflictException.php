<?php

declare(strict_types=1);

namespace LionTech\SDK\Exceptions\Business;

use LionTech\SDK\Exceptions\SdkException;

class ConflictException extends SdkException
{
    public function __construct(
        string $message = 'Request conflict with resource state',
        int $code = 409,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
