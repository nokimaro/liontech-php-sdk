<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\Requests\CreateRefundRequest;
use Nokimaro\LionTech\Responses\RefundResponse;

final readonly class RefundsClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    public function create(CreateRefundRequest $request): RefundResponse
    {
        $response = $this->apiClient->post('/api/v1/merchant/refunds', $request);

        return RefundResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function createWithId(string $refundId, CreateRefundRequest $request): RefundResponse
    {
        $response = $this->apiClient->put('/api/v1/merchant/refunds/' . $refundId, $request);

        return RefundResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function get(string $refundId): RefundResponse
    {
        $response = $this->apiClient->get('/api/v1/merchant/refunds/' . $refundId);

        return RefundResponse::fromArray(Json::decode((string) $response->getBody()));
    }
}
