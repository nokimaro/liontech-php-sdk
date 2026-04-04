<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Responses;

use Nokimaro\LionTech\Enums\RefundStatus;
use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\ValueObjects\Money;

final readonly class RefundResponse
{
    /**
     * @param string $refundId Refund ID
     * @param string $paymentId Payment ID
     * @param string|null $orderId Order ID
     * @param Money $amount Refund amount
     * @param Money|null $convAmount Converted amount
     * @param RefundStatus $status Refund status
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom fields
     * @param \DateTimeImmutable $createdAt Created at
     * @param string|null $txnId Transaction ID
     * @param string|null $rrn RRN
     */
    public function __construct(
        public string $refundId,
        public string $paymentId,
        public ?string $orderId = null,
        public Money $amount = new Money('0', \Nokimaro\LionTech\ValueObjects\Currency::USD),
        public ?Money $convAmount = null,
        public RefundStatus $status = RefundStatus::PENDING,
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
            \Nokimaro\LionTech\ValueObjects\Currency::USD
        );

        /** @var array<string, mixed>|null $convAmountRaw */
        $convAmountRaw = $data['convAmount'] ?? null;
        $convAmount = is_array($convAmountRaw) ? Money::fromArray($convAmountRaw) : null;

        return new self(
            refundId: Json::getString($data, 'refundId'),
            paymentId: Json::getString($data, 'paymentId'),
            orderId: Json::getNullableString($data, 'orderId'),
            amount: $amount,
            convAmount: $convAmount,
            status: RefundStatus::from($statusValue),
            webhookUrl: Json::getNullableString($data, 'webhookUrl'),
            customFields: Json::getNullableArray($data, 'customFields'),
            // @pest-mutate-ignore -- Defensive null check for createdAt
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
