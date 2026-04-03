<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\OrderStatus;
use LionTech\SDK\Json;
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
        /** @var array<string, mixed>|string $statusRaw */
        $statusRaw = $data['status'];
        $statusValue = is_array($statusRaw)
            ? Json::getString($statusRaw, 'value')
            : Json::getString($data, 'status');

        /** @var array<string, mixed> $amountData */
        $amountData = $data['amount'];
        /** @var array<string, mixed>|null $convAmountData */
        $convAmountData = $data['convAmount'] ?? null;
        /** @var array<string, mixed>|null $paidAmountData */
        $paidAmountData = $data['paidAmount'] ?? null;

        return new self(
            orderId: Json::getString($data, 'orderId'),
            amount: Money::fromArray($amountData),
            convAmount: $convAmountData !== null ? Money::fromArray($convAmountData) : null,
            paidAmount: $paidAmountData !== null ? Money::fromArray($paidAmountData) : null,
            status: OrderStatus::from($statusValue),
            payUrl: Json::getNullableString($data, 'payUrl'),
            successUrl: Json::getNullableString($data, 'successUrl'),
            declineUrl: Json::getNullableString($data, 'declineUrl'),
            webhookUrl: Json::getNullableString($data, 'webhookUrl'),
            customFields: Json::getNullableArray($data, 'customFields'),
            createdAt: new \DateTimeImmutable(Json::getString($data, 'createdAt')),
            expireAt: Json::getNullableString($data, 'expireAt') !== null
                ? new \DateTimeImmutable(Json::getString($data, 'expireAt'))
                : null,
            autoApprove: Json::getBool($data, 'autoApprove', true),
            description: Json::getNullableString($data, 'description'),
            items: isset($data['items']) && is_array($data['items']) ? array_values($data['items']) : null,
        );
    }
}
