<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Json;

final readonly class SavedPaymentMethod
{
    public function __construct(
        public string $paymentMethodId,
        public string $tokenId,
        public string $displayValue,
        public string $cardType,
        public string $cardExp,
        public bool $cardRequiresCvv,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentMethodId: Json::getString($data, 'payment_method_id'),
            tokenId: Json::getString($data, 'token_id'),
            displayValue: Json::getString($data, 'display_value'),
            cardType: Json::getString($data, 'card_type'),
            cardExp: Json::getString($data, 'card_exp'),
            cardRequiresCvv: Json::getBool($data, 'card_requires_cvv'),
        );
    }
}
