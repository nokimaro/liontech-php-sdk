<?php

declare(strict_types=1);

namespace LionTech\SDK\ValueObjects;

use JsonSerializable;

final readonly class Money implements JsonSerializable
{
    public function __construct(
        public string $amount,
        public Currency $currency,
    ) {}

    public static function fromString(string $amount, string $currencyCode): self
    {
        return new self($amount, Currency::from($currencyCode));
    }

    /**
     * @param array{value: string, currency: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['value'],
            currency: Currency::from($data['currency']),
        );
    }

    /**
     * @return array{value: string, currency: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->amount,
            'currency' => $this->currency->value,
        ];
    }
}
