<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Webhooks;

use Nokimaro\LionTech\Json;

final readonly class WebhookError
{
    public function __construct(
        public int $code,
        public string $description,
    ) {
    }

    public function hasError(): bool
    {
        return $this->code !== 0;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(code: Json::getInt($data, 'code'), description: Json::getString($data, 'description', ''));
    }
}
