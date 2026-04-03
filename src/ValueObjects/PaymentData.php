<?php

declare(strict_types=1);

namespace LionTech\SDK\ValueObjects;

use JsonSerializable;
use LionTech\SDK\DTOs\Request\SbpData;
use LionTech\SDK\Enums\PaymentMethodType;

final readonly class PaymentData implements JsonSerializable
{
    public function __construct(
        public PaymentMethodType $type,
        public EncryptedCardData|SbpData $object,
    ) {}

    public static function card(EncryptedCardData $cardData): self
    {
        return new self(
            type: PaymentMethodType::CARD,
            object: $cardData,
        );
    }

    public static function sbp(SbpData $sbpData): self
    {
        return new self(
            type: PaymentMethodType::SBP,
            object: $sbpData,
        );
    }

    /**
     * @return array{type: string, object: array}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->value,
            'object' => $this->object->jsonSerialize(),
        ];
    }
}
