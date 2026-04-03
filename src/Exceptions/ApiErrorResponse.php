<?php

declare(strict_types=1);

namespace LionTech\SDK\Exceptions;

class ApiErrorResponse
{
    /**
     * @param array<string, mixed> $details
     */
    public function __construct(
        public readonly int $code,
        public readonly string $description,
        public readonly ?string $traceId = null,
        public readonly array $details = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? 0,
            description: $data['description'] ?? 'Unknown error',
            traceId: $data['traceId'] ?? null,
            details: $data['details'] ?? [],
        );
    }
}
