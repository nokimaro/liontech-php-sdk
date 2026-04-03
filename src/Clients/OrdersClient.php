<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreateOrderRequest;
use LionTech\SDK\DTOs\Response\OrderResponse;
use LionTech\SDK\Http\HttpClient;

final class OrdersClient
{
    private const ORDERS_PATH = '/api/v1/merchant/orders';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {}

    /**
     * Create a new order with a PSP-generated ID.
     */
    public function create(CreateOrderRequest $request): OrderResponse
    {
        $response = $this->httpClient->post(self::ORDERS_PATH, $request);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return OrderResponse::fromArray($data);
    }

    /**
     * Create an order with a merchant-provided ID.
     */
    public function createWithId(string $orderId, CreateOrderRequest $request): OrderResponse
    {
        $response = $this->httpClient->put(self::ORDERS_PATH . '/' . $orderId, $request);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return OrderResponse::fromArray($data);
    }

    /**
     * Get order information.
     */
    public function get(string $orderId): OrderResponse
    {
        $response = $this->httpClient->get(self::ORDERS_PATH . '/' . $orderId);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return OrderResponse::fromArray($data);
    }

    /**
     * Cancel an order.
     */
    public function cancel(string $orderId): OrderResponse
    {
        $response = $this->httpClient->post(self::ORDERS_PATH . '/' . $orderId . '/cancel');
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return OrderResponse::fromArray($data);
    }

    /**
     * Close an order.
     */
    public function close(string $orderId): OrderResponse
    {
        $response = $this->httpClient->post(self::ORDERS_PATH . '/' . $orderId . '/close');
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return OrderResponse::fromArray($data);
    }
}
