<?php

declare(strict_types=1);

use Nokimaro\LionTech\Clients\OrdersClient;
use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Requests\CreateOrderRequest;
use Nokimaro\LionTech\Responses\OrderResponse;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

function createOrdersClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $ordersClient = new OrdersClient($apiClient);

    return [$apiClient, $ordersClient];
}

function orderData(array $overrides = []): array
{
    return array_merge([
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ], $overrides);
}

it('creates an order', function (): void {
    [$apiClient, $ordersClient] = createOrdersClient();
    $request = new CreateOrderRequest(amount: new Money('100.00', Currency::USD));

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/orders', $request)
        ->andReturn(mockResponse(orderData()));

    $result = $ordersClient->create($request);

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->orderId)
        ->toBe('ord_123');
});

it('creates an order with merchant id', function (): void {
    [$apiClient, $ordersClient] = createOrdersClient();
    $request = new CreateOrderRequest(amount: new Money('100.00', Currency::USD));

    $apiClient->shouldReceive('put')
        ->with('/api/v1/merchant/orders/ord_merchant_1', $request)
        ->andReturn(mockResponse(orderData()));

    $result = $ordersClient->createWithId('ord_merchant_1', $request);

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->orderId)
        ->toBe('ord_123');
});

it('gets an order', function (): void {
    [$apiClient, $ordersClient] = createOrdersClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/orders/ord_123')
        ->andReturn(mockResponse(orderData()));

    $result = $ordersClient->get('ord_123');

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->orderId)
        ->toBe('ord_123');
});

it('cancels an order', function (): void {
    [$apiClient, $ordersClient] = createOrdersClient();

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/orders/ord_123/cancel')
        ->andReturn(mockResponse(orderData([
            'status' => 'CANCELLED',
        ])));

    $result = $ordersClient->cancel('ord_123');

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->status->value)
        ->toBe('CANCELLED');
});

it('closes an order', function (): void {
    [$apiClient, $ordersClient] = createOrdersClient();

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/orders/ord_123/close')
        ->andReturn(mockResponse(orderData([
            'status' => 'PAID',
        ])));

    $result = $ordersClient->close('ord_123');

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->status->value)
        ->toBe('PAID');
});
