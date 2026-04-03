<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Response\PaymentResponse;
use LionTech\SDK\Enums\PaymentStatus;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

it('creates payment response from array', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->paymentId)->toBe('pay_123');
    expect($response->amount)->toBeInstanceOf(Money::class);
    expect($response->amount->amount)->toBe('100.00');
    expect($response->amount->currency)->toBe(Currency::USD);
    expect($response->status)->toBe(PaymentStatus::RECONCILED);
});

it('identifies redirect action', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T12:00:00Z',
        'additionalAction' => [
            'action' => 'redirect',
            'value' => 'https://acs.example.com/3ds',
        ],
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->requiresRedirect())->toBeTrue();
    expect($response->getRedirectUrl())->toBe('https://acs.example.com/3ds');
});

it('identifies no redirect action', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->requiresRedirect())->toBeFalse();
    expect($response->getRedirectUrl())->toBeNull();
});

it('checks if payment is final', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->isFinal())->toBeTrue();
    expect($response->isSuccessful())->toBeTrue();
});

it('checks if payment is declined', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'DECLINED',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->isFinal())->toBeTrue();
    expect($response->isSuccessful())->toBeFalse();
    expect($response->status->isDeclined())->toBeTrue();
});

it('is immutable', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);
    $reflection = new ReflectionClass($response);

    expect($reflection->isReadOnly())->toBeTrue();
});
