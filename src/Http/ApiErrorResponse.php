<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Http;

use Nokimaro\LionTech\Json;

final readonly class ApiErrorResponse
{
    /**
     * @param array<string, mixed> $details
     */
    public function __construct(
        public int $code,
        public string $description,
        public ?string $traceId = null,
        public array $details = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: Json::getInt($data, 'code'),
            description: Json::getString($data, 'description', 'Unknown error'),
            traceId: Json::getNullableString($data, 'traceId'),
            // @pest-mutate-ignore -- Defensive coalesce for API compatibility
            details: Json::getNullableArray($data, 'details') ?? [],
        );
    }
}
