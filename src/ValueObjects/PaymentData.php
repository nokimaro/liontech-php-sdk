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
    ) {
    }

    public static function card(EncryptedCardData $cardData): self
    {
        return new self(type: PaymentMethodType::CARD, object: $cardData);
    }

    public static function sbp(SbpData $sbpData): self
    {
        return new self(type: PaymentMethodType::SBP, object: $sbpData);
    }

    /**
     * @param array{type: string, object: array{encryptedCardData: string, cardHolder?: string}|array{bank: string}} $data
     */
    public static function fromArray(array $data): self
    {
        $type = PaymentMethodType::from($data['type']);

        $object = match ($type) {
            PaymentMethodType::CARD => new EncryptedCardData(
                encryptedCardData: $data['object']['encryptedCardData'],
                cardHolder: $data['object']['cardHolder'] ?? null,
            ),
            PaymentMethodType::SBP => new SbpData(bank: $data['object']['bank']),
        };

        return new self(type: $type, object: $object);
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
