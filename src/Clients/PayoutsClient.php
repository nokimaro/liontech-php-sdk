<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\Requests\CreatePayoutRequest;
use Nokimaro\LionTech\Responses\PayoutResponse;

final readonly class PayoutsClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    public function createWithId(string $payoutId, CreatePayoutRequest $request): PayoutResponse
    {
        $response = $this->apiClient->put('/api/v1/merchant/payouts/' . $payoutId, $request);

        return PayoutResponse::fromArray(Json::decodeObject((string) $response->getBody()));
    }

    public function get(string $payoutId): PayoutResponse
    {
        $response = $this->apiClient->get('/api/v1/merchant/payouts/' . $payoutId);

        return PayoutResponse::fromArray(Json::decodeObject((string) $response->getBody()));
    }
}
