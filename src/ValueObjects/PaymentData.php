<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\ValueObjects;

use JsonSerializable;
use Nokimaro\LionTech\Enums\PaymentMethodType;
use Nokimaro\LionTech\Json;

final readonly class PaymentData implements JsonSerializable
{
    public function __construct(
        public PaymentMethodType $type,
        public ?EncryptedCardData $object = null,
    ) {
    }

    public static function card(EncryptedCardData $cardData): self
    {
        return new self(type: PaymentMethodType::CARD, object: $cardData);
    }

    public static function sbp(): self
    {
        return new self(type: PaymentMethodType::SBP);
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

        return match ($type) {
            PaymentMethodType::CARD => new self(
                type: $type,
                object: new EncryptedCardData(
                    encryptedCardData: Json::getString($objectData, 'encryptedCardData'),
                    cardHolder: Json::getNullableString($objectData, 'cardHolder'),
                ),
            ),
            PaymentMethodType::SBP => new self(type: $type),
        };
    }

    /**
     * @return array{type: string, object: array<string, string>|\stdClass}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->value,
            'object' => $this->object?->jsonSerialize() ?? new \stdClass(),
        ];
    }
}
