<?php

declare(strict_types=1);

use Nokimaro\LionTech\Responses\OrderResponse;
use Nokimaro\LionTech\Responses\ResponseStatus;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

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
        ->toBeInstanceOf(ResponseStatus::class);
    expect($response->status->value)
        ->toBe('CREATED');
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

    expect($response->status->value)
        ->toBe('PAID');
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
            'changedAt' => '2024-01-15T10:00:00Z',
            'description' => 'Fully refunded',
        ],
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->status->value)
        ->toBe('REFUNDED');
    expect($response->status->changedAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
    expect($response->status->changedAt->format('Y-m-d'))
        ->toBe('2024-01-15');
    expect($response->status->description)
        ->toBe('Fully refunded');
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

it('handles expireAt as null', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'expireAt' => null,
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->expireAt)
        ->toBeNull();
});

it('handles items with multiple items', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'items' => [[
            'name' => 'Item 1',
        ], [
            'name' => 'Item 2',
        ]],
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->items)
        ->toHaveCount(2);
});

it('handles items as empty array', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'CREATED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'items' => [],
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->items)
        ->toBe([]);
});

it('status without changedAt and description defaults to null', function (): void {
    $data = [
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => [
            'value' => 'CREATED',
        ],
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->status->value)
        ->toBe('CREATED');
    expect($response->status->changedAt)
        ->toBeNull();
    expect($response->status->description)
        ->toBeNull();
});
