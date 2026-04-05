<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Requests;

use JsonSerializable;
use Nokimaro\LionTech\ValueObjects\Money;

final readonly class CreateRefundRequest implements JsonSerializable
{
    /**
     * @param Money $amount Refund amount
     * @param string $paymentId Payment ID to refund
     * @param string $webhookUrl Webhook URL (required by the API)
     * @param array<string, mixed>|null $customFields Custom metadata
     */
    public function __construct(
        public Money $amount,
        public string $paymentId,
        public string $webhookUrl,
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
            'paymentId' => $this->paymentId,
            'webhookUrl' => $this->webhookUrl,
        ];

        if ($this->customFields !== null) {
            $data['customFields'] = $this->customFields;
        }

        return $data;
    }
}
