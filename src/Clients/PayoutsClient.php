<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreatePayoutRequest;
use LionTech\SDK\DTOs\Response\PayoutResponse;
use LionTech\SDK\Http\HttpClient;

final class PayoutsClient
{
    private const PAYOUTS_PATH = '/api/v1/merchant/payouts';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {}

    /**
     * Create a payout with a merchant-provided ID.
     */
    public function createWithId(string $payoutId, CreatePayoutRequest $request): PayoutResponse
    {
        $response = $this->httpClient->put(self::PAYOUTS_PATH . '/' . $payoutId, $request);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return PayoutResponse::fromArray($data);
    }

    /**
     * Get payout information.
     */
    public function get(string $payoutId): PayoutResponse
    {
        $response = $this->httpClient->get(self::PAYOUTS_PATH . '/' . $payoutId);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return PayoutResponse::fromArray($data);
    }
}
