<?php

declare(strict_types=1);

namespace LionTech\SDK\Exceptions\Validation;

use LionTech\SDK\Exceptions\SdkException;

class ValidationException extends SdkException
{
    /** @var array<string, mixed> */
    private readonly array $errors;

    /**
     * @param array<string, mixed> $errors
     */
    public function __construct(
        string $message = 'Validation failed',
        int $code = 400,
        array $errors = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
