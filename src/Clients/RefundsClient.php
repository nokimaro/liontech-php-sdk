<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\DTOs\Response\RefundResponse;
use LionTech\SDK\Http\ApiClient;
use LionTech\SDK\Json;

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
