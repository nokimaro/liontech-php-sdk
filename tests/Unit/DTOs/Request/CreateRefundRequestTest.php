<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

it('creates refund request with minimal data', function (): void {
    $request = new CreateRefundRequest(amount: new Money('50.00', Currency::USD), paymentId: 'pay_123');

    expect($request->amount->amount)
        ->toBe('50.00');
    expect($request->paymentId)
        ->toBe('pay_123');
    expect($request->webhookUrl)
        ->toBeNull();
    expect($request->customFields)
        ->toBeNull();
});

it('creates refund request with all data', function (): void {
    $request = new CreateRefundRequest(
        amount: new Money('50.00', Currency::USD),
        paymentId: 'pay_123',
        webhookUrl: 'https://example.com/webhook',
        customFields: [
            'refund_reason' => 'customer_request',
        ],
    );

    expect($request->webhookUrl)
        ->toBe('https://example.com/webhook');
    expect($request->customFields)
        ->toBe([
            'refund_reason' => 'customer_request',
        ]);
});

it('serializes to JSON', function (): void {
    $request = new CreateRefundRequest(amount: new Money('50.00', Currency::USD), paymentId: 'pay_123');

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKeys(['amount', 'paymentId']);
    expect($json['amount'])->toBe([
        'value' => '50.00',
        'currency' => 'USD',
    ]);
    expect($json['paymentId'])->toBe('pay_123');
});

it('serializes optional fields when present', function (): void {
    $request = new CreateRefundRequest(
        amount: new Money('50.00', Currency::USD),
        paymentId: 'pay_123',
        webhookUrl: 'https://example.com/webhook',
        customFields: [
            'key' => 'value',
        ],
    );

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKeys(['webhookUrl', 'customFields']);
    expect($json['webhookUrl'])->toBe('https://example.com/webhook');
});

it('does not include null fields in JSON', function (): void {
    $request = new CreateRefundRequest(amount: new Money('50.00', Currency::USD), paymentId: 'pay_123');

    $json = $request->jsonSerialize();

    expect($json)
        ->not->toHaveKey('webhookUrl');
    expect($json)
        ->not->toHaveKey('customFields');
});

it('is immutable', function (): void {
    $request = new CreateRefundRequest(amount: new Money('50.00', Currency::USD), paymentId: 'pay_123');
    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
