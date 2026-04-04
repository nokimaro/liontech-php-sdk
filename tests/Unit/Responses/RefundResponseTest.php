<?php

declare(strict_types=1);

use Nokimaro\LionTech\Enums\RefundStatus;
use Nokimaro\LionTech\Responses\RefundResponse;
use Nokimaro\LionTech\ValueObjects\Money;

it('creates refund response from array', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->refundId)
        ->toBe('ref_123');
    expect($response->paymentId)
        ->toBe('pay_123');
    expect($response->amount)
        ->toBeInstanceOf(Money::class);
    expect($response->amount->amount)
        ->toBe('50.00');
    expect($response->status)
        ->toBe(RefundStatus::PENDING);
});

it('creates refund response with status as object', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => [
            'value' => 'SUCCEEDED',
        ],
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->status)
        ->toBe(RefundStatus::SUCCEEDED);
});

it('handles optional fields', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'orderId' => 'ord_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'convAmount' => [
            'value' => '45.00',
            'currency' => 'EUR',
        ],
        'status' => 'SUCCEEDED',
        'createdAt' => '2024-01-01T00:00:00Z',
        'webhookUrl' => 'https://example.com/webhook',
        'customFields' => [
            'key' => 'value',
        ],
        'txnId' => 'txn_123',
        'rrn' => 'rrn_123',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->orderId)
        ->toBe('ord_123');
    expect($response->convAmount)
        ->toBeInstanceOf(Money::class);
    expect($response->convAmount->amount)
        ->toBe('45.00');
    expect($response->webhookUrl)
        ->toBe('https://example.com/webhook');
    expect($response->txnId)
        ->toBe('txn_123');
    expect($response->rrn)
        ->toBe('rrn_123');
});

it('handles missing optional fields', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->orderId)
        ->toBeNull();
    expect($response->convAmount)
        ->toBeNull();
    expect($response->webhookUrl)
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
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->createdAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
});

it('checks if refund is final', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'SUCCEEDED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeTrue();
});

it('checks if refund is declined', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'DECLINED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeFalse();
});

it('checks pending refund is not final', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeFalse();
    expect($response->isSuccessful())
        ->toBeFalse();
});

it('is immutable', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);
    $reflection = new ReflectionClass($response);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});

it('handles status as string value', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'SUCCEEDED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->status)
        ->toBe(RefundStatus::SUCCEEDED);
});

it('checks is successful for declined status', function (): void {
    $data = [
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'DECLINED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = RefundResponse::fromArray($data);

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeFalse();
});
