<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Responses;

use Nokimaro\LionTech\Json;

final readonly class ResponseStatus
{
    /**
     * @param string $value Status value (e.g. CONFIRMED, PENDING)
     * @param \DateTimeImmutable|null $changedAt When the status changed
     * @param string|null $description Human-readable status description
     */
    public function __construct(
        public string $value,
        public ?\DateTimeImmutable $changedAt = null,
        public ?string $description = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: Json::getString($data, 'value'),
            changedAt: isset($data['changedAt'])
                ? new \DateTimeImmutable(Json::getString($data, 'changedAt'))
                : null,
            description: Json::getNullableString($data, 'description'),
        );
    }
}
