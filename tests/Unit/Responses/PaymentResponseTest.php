<?php

declare(strict_types=1);

use Nokimaro\LionTech\Responses\PaymentResponse;
use Nokimaro\LionTech\Responses\ResponseStatus;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

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

    expect($response->paymentId)
        ->toBe('pay_123');
    expect($response->amount)
        ->toBeInstanceOf(Money::class);
    expect($response->amount->amount)
        ->toBe('100.00');
    expect($response->amount->currency)
        ->toBe(Currency::USD);
    expect($response->status)
        ->toBeInstanceOf(ResponseStatus::class);
    expect($response->status->value)
        ->toBe('RECONCILED');
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

    expect($response->requiresRedirect())
        ->toBeTrue();
    expect($response->getRedirectUrl())
        ->toBe('https://acs.example.com/3ds');
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

    expect($response->requiresRedirect())
        ->toBeFalse();
    expect($response->getRedirectUrl())
        ->toBeNull();
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

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeTrue();
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

    expect($response->isFinal())
        ->toBeTrue();
    expect($response->isSuccessful())
        ->toBeFalse();
    expect($response->isDeclined())
        ->toBeTrue();
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

    expect($reflection->isReadOnly())
        ->toBeTrue();
});

it('handles payment method and additional action', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T12:00:00Z',
        'paymentMethod' => 'CARD',
        'paymentData' => [
            'three_ds' => true,
        ],
        'paymentToken' => [
            'token' => 'tok_123',
        ],
        'additionalAction' => [
            'action' => 'redirect',
            'value' => 'https://3ds.example.com',
        ],
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->paymentMethod)
        ->toBe('CARD');
    expect($response->paymentData)
        ->toBe([
            'three_ds' => true,
        ]);
    expect($response->paymentToken)
        ->toBe([
            'token' => 'tok_123',
        ]);
    expect($response->additionalAction)
        ->toBe([
            'action' => 'redirect',
            'value' => 'https://3ds.example.com',
        ]);
});

it('handles non-array additional action', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T12:00:00Z',
        'additionalAction' => 'not_an_array',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->additionalAction)
        ->toBeNull();
    expect($response->requiresRedirect())
        ->toBeFalse();
});

it('handles redirect with non-string value', function (): void {
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
            'value' => 123, // non-string value
        ],
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->requiresRedirect())
        ->toBeTrue();
    expect($response->getRedirectUrl())
        ->toBeNull();
});

it('handles items', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T12:00:00Z',
        'items' => [
            [
                'name' => 'Item 1',
                'price' => '50.00',
            ],
        ],
        'description' => 'Test payment',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->items)
        ->toHaveCount(1);
    expect($response->items[0]['name'])->toBe('Item 1');
    expect($response->description)
        ->toBe('Test payment');
});

it('handles missing amount', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->amount->amount)
        ->toBe('0');
    expect($response->amount->currency)
        ->toBe(Currency::USD);
});

it('handles status as object with all fields', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'EUR',
        ],
        'status' => [
            'value' => 'RECONCILED',
            'changedAt' => '2024-01-15T10:00:00Z',
            'description' => 'Payment confirmed',
        ],
        'createdAt' => '2024-01-01T12:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->status->value)
        ->toBe('RECONCILED');
    expect($response->status->changedAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
    expect($response->status->changedAt->format('Y-m-d'))
        ->toBe('2024-01-15');
    expect($response->status->description)
        ->toBe('Payment confirmed');
});

it('returns null redirect url when not requiring redirect', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T00:00:00Z',
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->requiresRedirect())
        ->toBeFalse();
    expect($response->getRedirectUrl())
        ->toBeNull();
});

it('handles items array with multiple items', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'RECONCILED',
        'createdAt' => '2024-01-01T00:00:00Z',
        'items' => [[
            'name' => 'Item 1',
        ], [
            'name' => 'Item 2',
        ]],
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->items)
        ->toHaveCount(2);
    expect($response->items[0]['name'])->toBe('Item 1');
    expect($response->items[1]['name'])->toBe('Item 2');
});

it('handles additionalAction with non-redirect type', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
        'additionalAction' => [
            'action' => 'something_else',
            'value' => 'url',
        ],
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->requiresRedirect())
        ->toBeFalse();
});

it('handles getRedirectUrl with missing value key', function (): void {
    $data = [
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
        'additionalAction' => [
            'action' => 'redirect',
        ],
    ];

    $response = PaymentResponse::fromArray($data);

    expect($response->requiresRedirect())
        ->toBeTrue();
    expect($response->getRedirectUrl())
        ->toBeNull();
});
