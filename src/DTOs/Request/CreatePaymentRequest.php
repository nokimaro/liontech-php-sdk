<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Request;

use JsonSerializable;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;

final readonly class CreatePaymentRequest implements JsonSerializable
{
    /**
     * @param Money $amount Payment amount
     * @param PaymentData $paymentData Payment method data
     * @param CustomerData|null $customer Customer information
     * @param string|null $orderId Associated order ID
     * @param bool $autoApprove Auto-approve payment
     * @param string|null $backLink URL to return after 3DS
     * @param string|null $webhookUrl Webhook URL
     * @param string|null $description Payment description
     * @param array<string, mixed>|null $customFields Custom metadata
     * @param array<string, mixed>|null $options Additional options
     */
    public function __construct(
        public Money $amount,
        public PaymentData $paymentData,
        public ?CustomerData $customer = null,
        public ?string $orderId = null,
        public bool $autoApprove = true,
        public ?string $backLink = null,
        public ?string $webhookUrl = null,
        public ?string $description = null,
        public ?array $customFields = null,
        public ?array $options = null,
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
            'autoApprove' => $this->autoApprove,
        ];

        if ($this->customer instanceof \LionTech\SDK\DTOs\Request\CustomerData) {
            $data['customer'] = $this->customer->jsonSerialize();
        }

        if ($this->orderId !== null) {
            $data['orderId'] = $this->orderId;
        }

        if ($this->backLink !== null) {
            $data['backLink'] = $this->backLink;
        }

        if ($this->webhookUrl !== null) {
            $data['webhookUrl'] = $this->webhookUrl;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->customFields !== null) {
            $data['customFields'] = $this->customFields;
        }

        if ($this->options !== null) {
            $data['options'] = $this->options;
        }

        return $data;
    }
}
