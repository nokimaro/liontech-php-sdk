<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Exceptions\Validation;

use Nokimaro\LionTech\Exceptions\SdkException;

class ValidationException extends SdkException
{
    /**
     * @param array<string, mixed> $errors
     */
    public function __construct(
        string $message = 'Validation failed',
        int $code = 400,
        private readonly array $errors = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
