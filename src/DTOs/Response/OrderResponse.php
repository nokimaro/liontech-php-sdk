<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\OrderStatus;
use LionTech\SDK\ValueObjects\Money;

final readonly class OrderResponse
{
    /**
     * @param string $orderId Order ID
     * @param Money $amount Order amount
     * @param Money|null $convAmount Converted amount
     * @param Money|null $paidAmount Paid amount
     * @param OrderStatus $status Order status
     * @param string|null $payUrl Payment URL
     * @param string|null $successUrl Success URL
     * @param string|null $declineUrl Decline URL
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom fields
     * @param \DateTimeImmutable $createdAt Created at
     * @param \DateTimeImmutable|null $expireAt Expire at
     * @param bool $autoApprove Auto approve
     * @param string|null $description Description
     * @param array<int, mixed>|null $items Order items
     */
    public function __construct(
        public string $orderId,
        public Money $amount,
        public ?Money $convAmount = null,
        public ?Money $paidAmount = null,
        public OrderStatus $status = OrderStatus::CREATED,
        public ?string $payUrl = null,
        public ?string $successUrl = null,
        public ?string $declineUrl = null,
        public ?string $webhookUrl = null,
        public ?array $customFields = null,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        public ?\DateTimeImmutable $expireAt = null,
        public bool $autoApprove = true,
        public ?string $description = null,
        public ?array $items = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            orderId: $data['orderId'],
            amount: Money::fromArray($data['amount']),
            convAmount: isset($data['convAmount']) ? Money::fromArray($data['convAmount']) : null,
            paidAmount: isset($data['paidAmount']) ? Money::fromArray($data['paidAmount']) : null,
            status: OrderStatus::from($data['status']['value'] ?? $data['status']),
            payUrl: $data['payUrl'] ?? null,
            successUrl: $data['successUrl'] ?? null,
            declineUrl: $data['declineUrl'] ?? null,
            webhookUrl: $data['webhookUrl'] ?? null,
            customFields: $data['customFields'] ?? null,
            createdAt: new \DateTimeImmutable($data['createdAt']),
            expireAt: isset($data['expireAt']) ? new \DateTimeImmutable($data['expireAt']) : null,
            autoApprove: $data['autoApprove'] ?? true,
            description: $data['description'] ?? null,
            items: $data['items'] ?? null,
        );
    }
}
