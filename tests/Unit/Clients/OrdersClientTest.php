<?php

declare(strict_types=1);

use LionTech\SDK\Clients\OrdersClient;
use LionTech\SDK\DTOs\Request\CreateOrderRequest;
use LionTech\SDK\DTOs\Response\OrderResponse;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createOrdersClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $ordersClient = new OrdersClient($httpClient);

    return [$client, $requestFactory, $httpClient, $ordersClient];
}

function mockOrderResponse($requestFactory, $client, string $method, string $url, string $jsonBody): void
{
    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with($method, $url)
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn($jsonBody);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);
}

function orderResponseJson(): string
{
    return json_encode([
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ]);
}

it('creates an order', function (): void {
    [$client, $requestFactory, $httpClient, $ordersClient] = createOrdersClientMock();

    mockOrderResponse(
        $requestFactory,
        $client,
        'POST',
        'https://api.example.com/api/v1/merchant/orders',
        orderResponseJson()
    );

    $request = new CreateOrderRequest(amount: new Money('100.00', Currency::USD));
    $result = $ordersClient->create($request);

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->orderId)
        ->toBe('ord_123');
});

it('creates an order with merchant id', function (): void {
    [$client, $requestFactory, $httpClient, $ordersClient] = createOrdersClientMock();

    mockOrderResponse(
        $requestFactory,
        $client,
        'PUT',
        'https://api.example.com/api/v1/merchant/orders/ord_merchant_1',
        orderResponseJson()
    );

    $request = new CreateOrderRequest(amount: new Money('100.00', Currency::USD));
    $result = $ordersClient->createWithId('ord_merchant_1', $request);

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->orderId)
        ->toBe('ord_123');
});

it('gets an order', function (): void {
    [$client, $requestFactory, $httpClient, $ordersClient] = createOrdersClientMock();

    mockOrderResponse(
        $requestFactory,
        $client,
        'GET',
        'https://api.example.com/api/v1/merchant/orders/ord_123',
        orderResponseJson()
    );

    $result = $ordersClient->get('ord_123');

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->orderId)
        ->toBe('ord_123');
});

it('cancels an order', function (): void {
    [$client, $requestFactory, $httpClient, $ordersClient] = createOrdersClientMock();

    mockOrderResponse(
        $requestFactory,
        $client,
        'POST',
        'https://api.example.com/api/v1/merchant/orders/ord_123/cancel',
        json_encode([
            'orderId' => 'ord_123',
            'amount' => [
                'value' => '100.00',
                'currency' => 'USD',
            ],
            'status' => 'CANCELLED',
            'createdAt' => '2024-01-01T00:00:00Z',
        ])
    );

    $result = $ordersClient->cancel('ord_123');

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->status->value)
        ->toBe('CANCELLED');
});

it('closes an order', function (): void {
    [$client, $requestFactory, $httpClient, $ordersClient] = createOrdersClientMock();

    mockOrderResponse(
        $requestFactory,
        $client,
        'POST',
        'https://api.example.com/api/v1/merchant/orders/ord_123/close',
        json_encode([
            'orderId' => 'ord_123',
            'amount' => [
                'value' => '100.00',
                'currency' => 'USD',
            ],
            'status' => 'PAID',
            'createdAt' => '2024-01-01T00:00:00Z',
        ])
    );

    $result = $ordersClient->close('ord_123');

    expect($result)
        ->toBeInstanceOf(OrderResponse::class);
    expect($result->status->value)
        ->toBe('PAID');
});
