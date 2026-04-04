<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Exceptions\Auth;

class TokenExpiredException extends AuthenticationException
{
    public function __construct(
        string $message = 'Access token has expired',
        int $code = 504,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
