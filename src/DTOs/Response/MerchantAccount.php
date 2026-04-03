<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Json;
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
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accountId: Json::getString($data, 'accountId'),
            accountTypeId: Json::getString($data, 'accountTypeId'),
            mstId: Json::getString($data, 'mstId'),
            currency: Currency::from(Json::getString($data, 'currency')),
            balance: Json::getString($data, 'balance'),
            createdAt: new \DateTimeImmutable(Json::getString($data, 'createdAt')),
            updatedAt: new \DateTimeImmutable(Json::getString($data, 'updatedAt')),
            validOn: new \DateTimeImmutable(Json::getString($data, 'validOn')),
        );
    }
}
