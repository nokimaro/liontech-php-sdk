<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\PayoutStatus;
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
        return new self(
            payoutId: $data['payoutId'],
            orderId: $data['orderId'] ?? null,
            amount: isset($data['amount']) ? Money::fromArray($data['amount']) : new Money(
                '0',
                \LionTech\SDK\ValueObjects\Currency::USD
            ),
            convAmount: isset($data['convAmount']) ? Money::fromArray($data['convAmount']) : null,
            status: PayoutStatus::from($data['status']['value'] ?? $data['status'] ?? 'PENDING'),
            paymentMethod: $data['paymentMethod'] ?? null,
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
