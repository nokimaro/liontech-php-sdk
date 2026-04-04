<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreatePayoutRequest;
use LionTech\SDK\DTOs\Response\PayoutResponse;
use LionTech\SDK\Http\ApiClient;
use LionTech\SDK\Json;

final readonly class PayoutsClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    public function createWithId(string $payoutId, CreatePayoutRequest $request): PayoutResponse
    {
        $response = $this->apiClient->put('/api/v1/merchant/payouts/' . $payoutId, $request);

        return PayoutResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function get(string $payoutId): PayoutResponse
    {
        $response = $this->apiClient->get('/api/v1/merchant/payouts/' . $payoutId);

        return PayoutResponse::fromArray(Json::decode((string) $response->getBody()));
    }
}
