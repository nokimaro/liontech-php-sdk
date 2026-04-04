<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Exceptions\Auth;

use Nokimaro\LionTech\Exceptions\SdkException;

class AuthenticationException extends SdkException
{
    public function __construct(
        string $message = 'Authentication failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
