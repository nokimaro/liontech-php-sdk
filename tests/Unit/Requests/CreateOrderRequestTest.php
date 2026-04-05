<?php

declare(strict_types=1);

use Nokimaro\LionTech\Requests\CreateOrderRequest;
use Nokimaro\LionTech\Requests\CustomerData;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

it('creates order request with minimal data', function (): void {
    $request = new CreateOrderRequest(amount: new Money('100.00', Currency::USD), description: 'Test order');

    expect($request->amount->amount)
        ->toBe('100.00');
    expect($request->amount->currency)
        ->toBe(Currency::USD);
    expect($request->description)
        ->toBe('Test order');
    expect($request->autoApprove)
        ->toBeTrue();
    expect($request->customer)
        ->toBeNull();
});

it('creates order request with all data', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        description: 'Test order',
        customer: new CustomerData(email: 'test@example.com'),
        autoApprove: false,
        customFields: [
            'order_number' => '12345',
        ],
        declineUrl: 'https://example.com/decline',
        successUrl: 'https://example.com/success',
        webhookUrl: 'https://example.com/webhook',
        expireAt: new DateTimeImmutable('2024-12-31'),
        options: [
            'option1' => 'value1',
        ],
    );

    expect($request->customer)
        ->not->toBeNull();
    expect($request->autoApprove)
        ->toBeFalse();
    expect($request->customFields)
        ->toBe([
            'order_number' => '12345',
        ]);
    expect($request->declineUrl)
        ->toBe('https://example.com/decline');
    expect($request->successUrl)
        ->toBe('https://example.com/success');
    expect($request->webhookUrl)
        ->toBe('https://example.com/webhook');
    expect($request->description)
        ->toBe('Test order');
});

it('serializes to JSON', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        description: 'Test order',
        autoApprove: true,
    );

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKeys(['amount', 'description', 'autoApprove']);
    expect($json['amount'])->toBe([
        'value' => '100.00',
        'currency' => 'USD',
    ]);
    expect($json['autoApprove'])->toBeTrue();
    expect($json['description'])->toBe('Test order');
});

it('serializes all optional fields to JSON', function (): void {
    $expireAt = new DateTimeImmutable('2024-12-31T23:59:59Z');
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        description: 'Test order',
        autoApprove: false,
        customFields: [
            'order_number' => '12345',
        ],
        declineUrl: 'https://example.com/decline',
        successUrl: 'https://example.com/success',
        webhookUrl: 'https://example.com/webhook',
        expireAt: $expireAt,
        options: [
            'option1' => 'value1',
        ],
    );

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKeys(
            [
                'amount',
                'autoApprove',
                'customFields',
                'declineUrl',
                'successUrl',
                'webhookUrl',
                'expireAt',
                'description',
                'options',
            ]
        );
    expect($json['customFields'])->toBe([
        'order_number' => '12345',
    ]);
    expect($json['declineUrl'])->toBe('https://example.com/decline');
    expect($json['successUrl'])->toBe('https://example.com/success');
    expect($json['webhookUrl'])->toBe('https://example.com/webhook');
    expect($json['expireAt'])->toBe('2024-12-31T23:59:59Z');
    expect($json['description'])->toBe('Test order');
    expect($json['options'])->toBe([
        'option1' => 'value1',
    ]);
});

it('serializes customer when present', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        description: 'Test order',
        customer: new CustomerData(email: 'test@example.com'),
    );

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKey('customer');
    expect($json['customer']['email'])->toBe('test@example.com');
});

it('is immutable', function (): void {
    $request = new CreateOrderRequest(amount: new Money('100.00', Currency::USD), description: 'Test order');
    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
