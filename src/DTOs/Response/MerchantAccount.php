<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\ValueObjects\Currency;

final readonly class MerchantAccount
{
    public function __construct(
        public string $accountId,
        public string $accountTypeId,
        public string $mstId,
        public Currency $currency,
        public string $balance,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public \DateTimeImmutable $validOn,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accountId: $data['accountId'],
            accountTypeId: $data['accountTypeId'],
            mstId: $data['mstId'],
            currency: Currency::from($data['currency']),
            balance: $data['balance'],
            createdAt: new \DateTimeImmutable($data['createdAt']),
            updatedAt: new \DateTimeImmutable($data['updatedAt']),
            validOn: new \DateTimeImmutable($data['validOn']),
        );
    }
}
