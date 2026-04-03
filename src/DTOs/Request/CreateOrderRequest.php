<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Request;

use JsonSerializable;
use LionTech\SDK\ValueObjects\Money;

final readonly class CreateOrderRequest implements JsonSerializable
{
    /**
     * @param Money $amount Order amount
     * @param CustomerData|null $customer Customer information
     * @param bool $autoApprove Auto-approve payments
     * @param array<string, mixed>|null $customFields Custom metadata
     * @param string|null $declineUrl URL to redirect on decline
     * @param string|null $successUrl URL to redirect on success
     * @param string|null $webhookUrl Webhook URL for notifications
     * @param \DateTimeImmutable|null $expireAt Order expiration time
     * @param string|null $description Order description
     * @param array<string, mixed>|null $options Additional options
     */
    public function __construct(
        public Money $amount,
        public ?CustomerData $customer = null,
        public bool $autoApprove = true,
        public ?array $customFields = null,
        public ?string $declineUrl = null,
        public ?string $successUrl = null,
        public ?string $webhookUrl = null,
        public ?\DateTimeImmutable $expireAt = null,
        public ?string $description = null,
        public ?array $options = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            'amount' => $this->amount->jsonSerialize(),
            'autoApprove' => $this->autoApprove,
        ];

        if ($this->customer !== null) {
            $data['customer'] = $this->customer->jsonSerialize();
        }

        if ($this->customFields !== null) {
            $data['customFields'] = $this->customFields;
        }

        if ($this->declineUrl !== null) {
            $data['declineUrl'] = $this->declineUrl;
        }

        if ($this->successUrl !== null) {
            $data['successUrl'] = $this->successUrl;
        }

        if ($this->webhookUrl !== null) {
            $data['webhookUrl'] = $this->webhookUrl;
        }

        if ($this->expireAt !== null) {
            $data['expireAt'] = $this->expireAt->format('Y-m-d\TH:i:s\Z');
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->options !== null) {
            $data['options'] = $this->options;
        }

        return $data;
    }
}
