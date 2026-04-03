<?php

declare(strict_types=1);

namespace LionTech\SDK\ValueObjects;

use JsonSerializable;
use LionTech\SDK\DTOs\Request\SbpData;
use LionTech\SDK\Enums\PaymentMethodType;
use LionTech\SDK\Json;

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
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $typeValue = $data['type'];
        $type = PaymentMethodType::from(is_string($typeValue) ? $typeValue : 'card');

        /** @var array<string, mixed> $objectData */
        $objectData = $data['object'];

        $object = match ($type) {
            PaymentMethodType::CARD => new EncryptedCardData(
                encryptedCardData: Json::getString($objectData, 'encryptedCardData'),
                cardHolder: Json::getNullableString($objectData, 'cardHolder'),
            ),
            PaymentMethodType::SBP => new SbpData(bank: Json::getString($objectData, 'bank')),
        };

        return new self(type: $type, object: $object);
    }

    /**
     * @return array{type: string, object: array<string, mixed>}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->value,
            'object' => $this->object->jsonSerialize(),
        ];
    }
}
