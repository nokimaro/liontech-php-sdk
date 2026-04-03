<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\RefundStatus;
use LionTech\SDK\ValueObjects\Money;

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
        public Money $amount = new Money('0', \LionTech\SDK\ValueObjects\Currency::USD),
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
        return new self(
            refundId: $data['refundId'],
            paymentId: $data['paymentId'],
            orderId: $data['orderId'] ?? null,
            amount: isset($data['amount']) ? Money::fromArray($data['amount']) : new Money(
                '0',
                \LionTech\SDK\ValueObjects\Currency::USD
            ),
            convAmount: isset($data['convAmount']) ? Money::fromArray($data['convAmount']) : null,
            status: RefundStatus::from($data['status']['value'] ?? $data['status'] ?? 'PENDING'),
            webhookUrl: $data['webhookUrl'] ?? null,
            customFields: $data['customFields'] ?? null,
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable(
                $data['createdAt']
            ) : new \DateTimeImmutable(),
            txnId: $data['txnId'] ?? null,
            rrn: $data['rrn'] ?? null,
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
