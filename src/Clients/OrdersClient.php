<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\Requests\CreateOrderRequest;
use Nokimaro\LionTech\Responses\OrderResponse;

final readonly class OrdersClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    public function create(CreateOrderRequest $request): OrderResponse
    {
        $response = $this->apiClient->post('/api/v1/merchant/orders', $request);

        return OrderResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function createWithId(string $orderId, CreateOrderRequest $request): OrderResponse
    {
        $response = $this->apiClient->put('/api/v1/merchant/orders/' . $orderId, $request);

        return OrderResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function get(string $orderId): OrderResponse
    {
        $response = $this->apiClient->get('/api/v1/merchant/orders/' . $orderId);

        return OrderResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function cancel(string $orderId): OrderResponse
    {
        $response = $this->apiClient->post('/api/v1/merchant/orders/' . $orderId . '/cancel');

        return OrderResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function close(string $orderId): OrderResponse
    {
        $response = $this->apiClient->post('/api/v1/merchant/orders/' . $orderId . '/close');

        return OrderResponse::fromArray(Json::decode((string) $response->getBody()));
    }
}
