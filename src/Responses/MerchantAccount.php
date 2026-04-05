<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Responses;

use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

final readonly class MerchantAccount
{
    public function __construct(
        public string $accountId,
        public string $accountTypeId,
        public string $mstId,
        public Currency $currency,
        public Money $balance,
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
        /** @var array<string, mixed>|null $balanceRaw */
        $balanceRaw = $data['balance'] ?? null;
        $balance = is_array($balanceRaw)
            ? Money::fromArray($balanceRaw)
            : new Money('0', Currency::from(Json::getString($data, 'currency')));

        return new self(
            accountId: Json::getString($data, 'accountId'),
            accountTypeId: Json::getString($data, 'accountTypeId'),
            mstId: Json::getString($data, 'mstId'),
            currency: Currency::from(Json::getString($data, 'currency')),
            balance: $balance,
            createdAt: new \DateTimeImmutable(Json::getString($data, 'createdAt')),
            updatedAt: new \DateTimeImmutable(Json::getString($data, 'updatedAt')),
            validOn: new \DateTimeImmutable(Json::getString($data, 'validOn')),
        );
    }
}
