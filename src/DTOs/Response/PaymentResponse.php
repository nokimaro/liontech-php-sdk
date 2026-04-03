<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\PaymentStatus;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;

final readonly class PaymentResponse
{
    /**
     * @param string $paymentId Payment ID
     * @param string|null $orderId Order ID
     * @param Money $amount Payment amount
     * @param Money|null $convAmount Converted amount
     * @param PaymentStatus $status Payment status
     * @param PaymentData|null $paymentMethod Payment method
     * @param array<string, mixed>|null $paymentData Payment data
     * @param array<string, mixed>|null $paymentToken Payment token
     * @param array<string, mixed>|null $additionalAction Additional action (e.g., redirect)
     * @param bool $autoApprove Auto approve
     * @param string|null $backLink Back link URL
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom fields
     * @param \DateTimeImmutable $createdAt Created at
     * @param string|null $description Description
     * @param array<int, mixed>|null $items Payment items
     * @param string|null $txnId Transaction ID
     * @param string|null $rrn RRN
     */
    public function __construct(
        public string $paymentId,
        public ?string $orderId = null,
        public Money $amount = new Money('0', \LionTech\SDK\ValueObjects\Currency::USD),
        public ?Money $convAmount = null,
        public PaymentStatus $status = PaymentStatus::OPERATION,
        public ?PaymentData $paymentMethod = null,
        public ?array $paymentData = null,
        public ?array $paymentToken = null,
        public ?array $additionalAction = null,
        public bool $autoApprove = true,
        public ?string $backLink = null,
        public ?string $webhookUrl = null,
        public ?array $customFields = null,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        public ?string $description = null,
        public ?array $items = null,
        public ?string $txnId = null,
        public ?string $rrn = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentId: $data['paymentId'],
            orderId: $data['orderId'] ?? null,
            amount: isset($data['amount']) ? Money::fromArray($data['amount']) : new Money('0', \LionTech\SDK\ValueObjects\Currency::USD),
            convAmount: isset($data['convAmount']) ? Money::fromArray($data['convAmount']) : null,
            status: PaymentStatus::from($data['status']['value'] ?? $data['status'] ?? 'OPERATION'),
            paymentMethod: isset($data['paymentMethod']) ? PaymentData::fromArray($data['paymentMethod']) : null,
            paymentData: $data['paymentData'] ?? null,
            paymentToken: $data['paymentToken'] ?? null,
            additionalAction: $data['additionalAction'] ?? null,
            autoApprove: $data['autoApprove'] ?? true,
            backLink: $data['backLink'] ?? null,
            webhookUrl: $data['webhookUrl'] ?? null,
            customFields: $data['customFields'] ?? null,
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable($data['createdAt']) : new \DateTimeImmutable(),
            description: $data['description'] ?? null,
            items: $data['items'] ?? null,
            txnId: $data['txnId'] ?? null,
            rrn: $data['rrn'] ?? null,
        );
    }

    public function requiresRedirect(): bool
    {
        return $this->additionalAction !== null
            && ($this->additionalAction['action'] ?? null) === 'redirect';
    }

    public function getRedirectUrl(): ?string
    {
        return $this->requiresRedirect() ? ($this->additionalAction['value'] ?? null) : null;
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
