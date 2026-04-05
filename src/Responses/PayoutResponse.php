<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Responses;

use Nokimaro\LionTech\Enums\PayoutStatus;
use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\ValueObjects\Money;

final readonly class PayoutResponse
{
    /**
     * @param string $payoutId Payout ID
     * @param string|null $orderId Order ID
     * @param Money $amount Payout amount
     * @param Money|null $convAmount Converted amount
     * @param ResponseStatus $status Payout status
     * @param string|null $paymentMethod Payment method
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom fields
     * @param \DateTimeImmutable $createdAt Created at
     * @param string|null $txnId Transaction ID
     * @param string|null $rrn RRN
     */
    public function __construct(
        public string $payoutId,
        public ?string $orderId = null,
        public Money $amount = new Money('0', \Nokimaro\LionTech\ValueObjects\Currency::USD),
        public ?Money $convAmount = null,
        public ResponseStatus $status = new ResponseStatus(''),
        public ?string $paymentMethod = null,
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
        /** @var array<string, mixed>|string|null $statusRaw */
        $statusRaw = $data['status'] ?? null;
        $status = is_array($statusRaw)
            ? ResponseStatus::fromArray($statusRaw)
            : new ResponseStatus(value: is_string($statusRaw) ? $statusRaw : '');

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
            payoutId: Json::getString($data, 'payoutId'),
            orderId: Json::getNullableString($data, 'orderId'),
            amount: $amount,
            convAmount: $convAmount,
            status: $status,
            paymentMethod: Json::getNullableString($data, 'paymentMethod'),
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
        return PayoutStatus::tryFrom($this->status->value)?->isFinal() ?? false;
    }

    public function isSuccessful(): bool
    {
        return PayoutStatus::tryFrom($this->status->value)?->isSuccessful() ?? false;
    }
}
