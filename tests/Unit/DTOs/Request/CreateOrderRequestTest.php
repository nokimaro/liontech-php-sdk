<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\CreateOrderRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

it('creates order request with minimal data', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
    );

    expect($request->amount->amount)->toBe('100.00');
    expect($request->amount->currency)->toBe(Currency::USD);
    expect($request->autoApprove)->toBeTrue();
    expect($request->customer)->toBeNull();
});

it('creates order request with all data', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        customer: new CustomerData(email: 'test@example.com'),
        autoApprove: false,
        customFields: ['order_number' => '12345'],
        declineUrl: 'https://example.com/decline',
        successUrl: 'https://example.com/success',
        webhookUrl: 'https://example.com/webhook',
        expireAt: new DateTimeImmutable('2024-12-31'),
        description: 'Test order',
        options: ['option1' => 'value1'],
    );

    expect($request->customer)->not->toBeNull();
    expect($request->autoApprove)->toBeFalse();
    expect($request->customFields)->toBe(['order_number' => '12345']);
    expect($request->declineUrl)->toBe('https://example.com/decline');
    expect($request->successUrl)->toBe('https://example.com/success');
    expect($request->webhookUrl)->toBe('https://example.com/webhook');
    expect($request->description)->toBe('Test order');
});

it('serializes to JSON', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        autoApprove: true,
        description: 'Test order',
    );

    $json = $request->jsonSerialize();

    expect($json)->toHaveKeys(['amount', 'autoApprove', 'description']);
    expect($json['amount'])->toBe(['value' => '100.00', 'currency' => 'USD']);
    expect($json['autoApprove'])->toBeTrue();
    expect($json['description'])->toBe('Test order');
});

it('serializes customer when present', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
        customer: new CustomerData(email: 'test@example.com'),
    );

    $json = $request->jsonSerialize();

    expect($json)->toHaveKey('customer');
    expect($json['customer']['email'])->toBe('test@example.com');
});

it('is immutable', function (): void {
    $request = new CreateOrderRequest(
        amount: new Money('100.00', Currency::USD),
    );
    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())->toBeTrue();
});
