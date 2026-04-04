<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

use LionTech\SDK\Enums\PaymentStatus;
use LionTech\SDK\Json;
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

        /** @var array<string, mixed>|null $paymentMethodRaw */
        $paymentMethodRaw = $data['paymentMethod'] ?? null;
        // @pest-mutate-ignore -- Defensive null check
        $paymentMethod = is_array($paymentMethodRaw) ? PaymentData::fromArray($paymentMethodRaw) : null;

        /** @var array<string, mixed>|null $additionalActionRaw */
        $additionalActionRaw = $data['additionalAction'] ?? null;
        // @pest-mutate-ignore -- Defensive null check
        $additionalAction = is_array($additionalActionRaw) ? $additionalActionRaw : null;

        return new self(
            paymentId: Json::getString($data, 'paymentId'),
            orderId: Json::getNullableString($data, 'orderId'),
            amount: $amount,
            convAmount: $convAmount,
            status: PaymentStatus::from($statusValue),
            paymentMethod: $paymentMethod,
            paymentData: Json::getNullableArray($data, 'paymentData'),
            paymentToken: Json::getNullableArray($data, 'paymentToken'),
            additionalAction: $additionalAction,
            autoApprove: Json::getBool($data, 'autoApprove', true),
            backLink: Json::getNullableString($data, 'backLink'),
            webhookUrl: Json::getNullableString($data, 'webhookUrl'),
            customFields: Json::getNullableArray($data, 'customFields'),
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable(Json::getString(
                $data,
                'createdAt'
            )) : new \DateTimeImmutable(),
            description: Json::getNullableString($data, 'description'),
            // @pest-mutate-ignore -- array_values is structural
            items: isset($data['items']) && is_array($data['items']) ? array_values($data['items']) : null,
            txnId: Json::getNullableString($data, 'txnId'),
            rrn: Json::getNullableString($data, 'rrn'),
        );
    }

    public function requiresRedirect(): bool
    {
        return $this->additionalAction !== null
            && ($this->additionalAction['action'] ?? null) === 'redirect';
    }

    public function getRedirectUrl(): ?string
    {
        if (! $this->requiresRedirect()) {
            return null;
        }

        $value = $this->additionalAction['value'] ?? null;

        return is_string($value) ? $value : null;
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
