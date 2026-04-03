<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreatePayoutRequest;
use LionTech\SDK\DTOs\Response\PayoutResponse;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class PayoutsClient
{
    private const string PAYOUTS_PATH = '/api/v1/merchant/payouts';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * Create a payout with a merchant-provided ID.
     */
    public function createWithId(string $payoutId, CreatePayoutRequest $request): PayoutResponse
    {
        $response = $this->httpClient->put(self::PAYOUTS_PATH . '/' . $payoutId, $request);
        $data = Json::decode((string) $response->getBody());

        return PayoutResponse::fromArray($data);
    }

    /**
     * Get payout information.
     */
    public function get(string $payoutId): PayoutResponse
    {
        $response = $this->httpClient->get(self::PAYOUTS_PATH . '/' . $payoutId);
        $data = Json::decode((string) $response->getBody());

        return PayoutResponse::fromArray($data);
    }
}
