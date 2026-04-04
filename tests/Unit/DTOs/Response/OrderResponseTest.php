<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Response\OrderResponse;
use LionTech\SDK\Enums\OrderStatus;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

it('creates order response from array', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'autoApprove' => true,
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->orderId)
        ->toBe('ord_123');
    expect($response->amount)
        ->toBeInstanceOf(Money::class);
    expect($response->amount->amount)
        ->toBe('100.00');
    expect($response->amount->currency)
        ->toBe(Currency::USD);
    expect($response->status)
        ->toBe(OrderStatus::CREATED);
    expect($response->autoApprove)
        ->toBeTrue();
});

it('creates order response with status as string', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PAID',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->status)
        ->toBe(OrderStatus::PAID);
});

it('creates order response with status as object', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => [
            'value' => 'REFUNDED',
        ],
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->status)
        ->toBe(OrderStatus::REFUNDED);
});

it('handles optional fields', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'payUrl' => 'https://pay.example.com/123',
        'successUrl' => 'https://example.com/success',
        'declineUrl' => 'https://example.com/decline',
        'webhookUrl' => 'https://example.com/webhook',
        'customFields' => [
            'order_number' => '12345',
        ],
        'description' => 'Test order',
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->payUrl)
        ->toBe('https://pay.example.com/123');
    expect($response->successUrl)
        ->toBe('https://example.com/success');
    expect($response->declineUrl)
        ->toBe('https://example.com/decline');
    expect($response->webhookUrl)
        ->toBe('https://example.com/webhook');
    expect($response->customFields)
        ->toBe([
            'order_number' => '12345',
        ]);
    expect($response->description)
        ->toBe('Test order');
});

it('is immutable', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = OrderResponse::fromArray($data);
    $reflection = new ReflectionClass($response);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});

it('handles items and expireAt', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'expireAt' => '2024-12-31T23:59:59Z',
        'items' => [
            [
                'name' => 'Item 1',
                'price' => '50.00',
            ],
            [
                'name' => 'Item 2',
                'price' => '50.00',
            ],
        ],
        'convAmount' => [
            'value' => '90.00',
            'currency' => 'EUR',
        ],
        'paidAmount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->expireAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
    expect($response->expireAt->format('Y-m-d'))
        ->toBe('2024-12-31');
    expect($response->items)
        ->toHaveCount(2);
    expect($response->items[0]['name'])->toBe('Item 1');
    expect($response->convAmount)
        ->toBeInstanceOf(Money::class);
    expect($response->paidAmount)
        ->toBeInstanceOf(Money::class);
});
