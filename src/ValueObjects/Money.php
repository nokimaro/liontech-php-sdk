<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\ValueObjects;

use JsonSerializable;

final readonly class Money implements JsonSerializable
{
    public function __construct(
        public string $amount,
        public Currency $currency,
    ) {
    }

    public static function fromString(string $amount, string $currencyCode): self
    {
        return new self($amount, Currency::from($currencyCode));
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $value = $data['value'];
        $currency = $data['currency'];
        // @pest-mutate-ignore -- Defensive type checks for API robustness
        return new self(
            amount: is_string($value) ? $value : (is_numeric($value) ? (string) $value : '0'),
            currency: Currency::from(is_string($currency) ? $currency : 'USD'),
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
