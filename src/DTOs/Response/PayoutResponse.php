<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\PayoutStatus;
use LionTech\SDK\Json;
use LionTech\SDK\ValueObjects\Money;

final readonly class PayoutResponse
{
    /**
     * @param string $payoutId Payout ID
     * @param string|null $orderId Order ID
     * @param Money $amount Payout amount
     * @param Money|null $convAmount Converted amount
     * @param PayoutStatus $status Payout status
     * @param array<string, mixed>|null $paymentMethod Payment method
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom fields
     * @param \DateTimeImmutable $createdAt Created at
     * @param string|null $txnId Transaction ID
     * @param string|null $rrn RRN
     */
    public function __construct(
        public string $payoutId,
        public ?string $orderId = null,
        public Money $amount = new Money('0', \LionTech\SDK\ValueObjects\Currency::USD),
        public ?Money $convAmount = null,
        public PayoutStatus $status = PayoutStatus::PENDING,
        public ?array $paymentMethod = null,
        public ?string $webhookUrl = null,
        public ?array $customFields = null,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        public ?string $txnId = null,
        public ?string $rrn = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array<string, mixed>|string $statusRaw */
        $statusRaw = $data['status'];
        $statusValue = is_array($statusRaw)
            ? Json::getString($statusRaw, 'value')
            : Json::getString($data, 'status');

        /** @var array<string, mixed>|null $amountRaw */
        $amountRaw = $data['amount'] ?? null;
        $amount = is_array($amountRaw) ? Money::fromArray($amountRaw) : new Money(
            '0',
            \LionTech\SDK\ValueObjects\Currency::USD
        );

        /** @var array<string, mixed>|null $convAmountRaw */
        $convAmountRaw = $data['convAmount'] ?? null;
        $convAmount = is_array($convAmountRaw) ? Money::fromArray($convAmountRaw) : null;

        return new self(
            payoutId: Json::getString($data, 'payoutId'),
            orderId: Json::getNullableString($data, 'orderId'),
            amount: $amount,
            convAmount: $convAmount,
            status: PayoutStatus::from($statusValue),
            paymentMethod: Json::getNullableArray($data, 'paymentMethod'),
            webhookUrl: Json::getNullableString($data, 'webhookUrl'),
            customFields: Json::getNullableArray($data, 'customFields'),
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable(Json::getString(
                $data,
                'createdAt'
            )) : new \DateTimeImmutable(),
            txnId: Json::getNullableString($data, 'txnId'),
            rrn: Json::getNullableString($data, 'rrn'),
        );
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    public function isSuccessful(): bool
    {
        return $this->status->isSuccessful();
    }
}
