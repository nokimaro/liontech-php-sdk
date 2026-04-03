<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

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
            paymentMethodId: $data['payment_method_id'],
            tokenId: $data['token_id'],
            displayValue: $data['display_value'],
            cardType: $data['card_type'],
            cardExp: $data['card_exp'],
            cardRequiresCvv: $data['card_requires_cvv'],
        );
    }
}
