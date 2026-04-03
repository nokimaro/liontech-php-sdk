<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Request;

use JsonSerializable;
use LionTech\SDK\ValueObjects\Money;

final readonly class CreateRefundRequest implements JsonSerializable
{
    /**
     * @param Money $amount Refund amount
     * @param string $paymentId Payment ID to refund
     * @param string|null $webhookUrl Webhook URL
     * @param array<string, mixed>|null $customFields Custom metadata
     */
    public function __construct(
        public Money $amount,
        public string $paymentId,
        public ?string $webhookUrl = null,
        public ?array $customFields = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            'amount' => $this->amount->jsonSerialize(),
            'paymentId' => $this->paymentId,
        ];

        if ($this->webhookUrl !== null) {
            $data['webhookUrl'] = $this->webhookUrl;
        }

        if ($this->customFields !== null) {
            $data['customFields'] = $this->customFields;
        }

        return $data;
    }
}
