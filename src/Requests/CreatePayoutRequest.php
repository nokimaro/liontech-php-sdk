<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Requests;

use JsonSerializable;
use Nokimaro\LionTech\ValueObjects\Money;
use Nokimaro\LionTech\ValueObjects\PaymentData;

final readonly class CreatePayoutRequest implements JsonSerializable
{
    /**
     * @param Money $amount Payout amount
     * @param PaymentData $paymentData Payout method data
     * @param CustomerData|null $customer Customer information
     * @param string|null $orderId Associated order ID
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom metadata
     */
    public function __construct(
        public Money $amount,
        public PaymentData $paymentData,
        public ?CustomerData $customer = null,
        public ?string $orderId = null,
        public ?string $webhookUrl = null,
        public ?array $customFields = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            'amount' => $this->amount->jsonSerialize(),
            'paymentData' => $this->paymentData->jsonSerialize(),
        ];

        if ($this->customer instanceof \Nokimaro\LionTech\Requests\CustomerData) {
            $data['customer'] = $this->customer->jsonSerialize();
        }

        if ($this->orderId !== null) {
            $data['orderId'] = $this->orderId;
        }

        if ($this->webhookUrl !== null) {
            $data['webhookUrl'] = $this->webhookUrl;
        }

        if ($this->customFields !== null) {
            $data['customFields'] = $this->customFields;
        }

        return $data;
    }
}
