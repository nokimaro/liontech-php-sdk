<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Webhooks;

use Nokimaro\LionTech\Enums\WebhookEventType;
use Nokimaro\LionTech\Responses\PaymentResponse;

final readonly class WebhookPayload
{
    public function __construct(
        public WebhookEventType $type,
        public PaymentResponse $payment,
        public ?WebhookError $error = null,
    ) {
    }

    public static function fromJson(string $json): self
    {
        /** @var mixed $decoded */
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new \InvalidArgumentException('Invalid webhook JSON payload.');
        }

        /** @var array<string, mixed> $decoded */
        return self::fromArray($decoded);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $typeValue = $data['type'] ?? '';
        $type = WebhookEventType::from(is_string($typeValue) ? $typeValue : '');

        /** @var array<string, mixed> $objectData */
        $objectData = is_array($data['object'] ?? null) ? $data['object'] : [];

        /** @var array<string, mixed>|null $errorData */
        $errorData = is_array($data['error'] ?? null) ? $data['error'] : null;

        return new self(
            type: $type,
            payment: PaymentResponse::fromArray($objectData),
            error: $errorData !== null ? WebhookError::fromArray($errorData) : null,
        );
    }
}
