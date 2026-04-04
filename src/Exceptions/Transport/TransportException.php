<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Exceptions\Transport;

use Nokimaro\LionTech\Exceptions\SdkException;

class TransportException extends SdkException
{
    public function __construct(
        string $message = 'HTTP transport error occurred',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
