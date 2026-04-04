<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Response\PayoutResponse;
use LionTech\SDK\Enums\PayoutStatus;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

it('creates payout response from array', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->payoutId)
        ->toBe('payout_123');
    expect($response->amount)
        ->toBeInstanceOf(Money::class);
    expect($response->amount->amount)
        ->toBe('500.00');
    expect($response->amount->currency)
        ->toBe(Currency::USD);
    expect($response->status)
        ->toBe(PayoutStatus::PENDING);
});

it('creates payout response with status as object', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => [
            'value' => 'SUCCEEDED',
        ],
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->status)
        ->toBe(PayoutStatus::SUCCEEDED);
});

it('handles optional fields', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'convAmount' => [
            'value' => '450.00',
            'currency' => 'EUR',
        ],
        'status' => 'SUCCEEDED',
        'createdAt' => '2024-01-01T00:00:00Z',
        'paymentMethod' => [
            'type' => 'card',
        ],
        'webhookUrl' => 'https://example.com/webhook',
        'customFields' => [
            'key' => 'value',
        ],
        'txnId' => 'txn_123',
        'rrn' => 'rrn_123',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->orderId)
        ->toBe('ord_123');
    expect($response->convAmount)
        ->toBeInstanceOf(Money::class);
    expect($response->convAmount->amount)
        ->toBe('450.00');
    expect($response->paymentMethod)
        ->toBe([
            'type' => 'card',
        ]);
    expect($response->webhookUrl)
        ->toBe('https://example.com/webhook');
    expect($response->txnId)
        ->toBe('txn_123');
    expect($response->rrn)
        ->toBe('rrn_123');
});

it('handles missing optional fields', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->orderId)
        ->toBeNull();
    expect($response->convAmount)
        ->toBeNull();
    expect($response->paymentMethod)
        ->toBeNull();
    expect($response->customFields)
        ->toBeNull();
    expect($response->txnId)
        ->toBeNull();
    expect($response->rrn)
        ->toBeNull();
});

it('handles missing createdAt', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->createdAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
});

it('checks if payout is final', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'SUCCEEDED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeTrue();
});

it('checks if payout is declined', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'DECLINED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeFalse();
});

it('checks pending payout is not final', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeFalse();
    expect($response->isSuccessful())
        ->toBeFalse();
});

it('is immutable', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => [
            'value' => '500.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);
    $reflection = new ReflectionClass($response);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});

it('handles status as string value', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => ['value' => '500.00', 'currency' => 'USD'],
        'status' => 'SUCCEEDED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->status)->toBe(PayoutStatus::SUCCEEDED);
});

it('checks is successful for declined status', function (): void {
    $data = [
        'payoutId' => 'payout_123',
        'amount' => ['value' => '500.00', 'currency' => 'USD'],
        'status' => 'DECLINED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PayoutResponse::fromArray($data);

    expect($response->isFinal())->toBeTrue();
    expect($response->isSuccessful())->toBeFalse();
});
