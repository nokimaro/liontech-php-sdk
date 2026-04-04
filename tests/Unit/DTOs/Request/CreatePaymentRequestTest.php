<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\CreatePaymentRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\Enums\PaymentMethodType;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\EncryptedCardData;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;

it('creates payment request with minimal data', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePaymentRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
    );

    expect($request->amount->amount)->toBe('100.00');
    expect($request->autoApprove)->toBeTrue();
    expect($request->customer)->toBeNull();
    expect($request->orderId)->toBeNull();
});

it('creates payment request with all data', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePaymentRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
        customer: new CustomerData(email: 'test@example.com'),
        orderId: 'ord_123',
        autoApprove: false,
        backLink: 'https://example.com/back',
        webhookUrl: 'https://example.com/webhook',
        description: 'Test payment',
        customFields: ['order_number' => '12345'],
        options: ['option1' => 'value1'],
    );

    expect($request->customer)->not->toBeNull();
    expect($request->orderId)->toBe('ord_123');
    expect($request->autoApprove)->toBeFalse();
    expect($request->backLink)->toBe('https://example.com/back');
    expect($request->webhookUrl)->toBe('https://example.com/webhook');
    expect($request->description)->toBe('Test payment');
});

it('serializes to JSON', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePaymentRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
        autoApprove: true,
        description: 'Test payment',
    );

    $json = $request->jsonSerialize();

    expect($json)->toHaveKeys(['amount', 'paymentData', 'autoApprove', 'description']);
    expect($json['amount'])->toBe(['value' => '100.00', 'currency' => 'USD']);
    expect($json['autoApprove'])->toBeTrue();
    expect($json['description'])->toBe('Test payment');
});

it('serializes optional fields when present', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePaymentRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
        customer: new CustomerData(email: 'test@example.com'),
        orderId: 'ord_123',
        backLink: 'https://example.com/back',
        webhookUrl: 'https://example.com/webhook',
        customFields: ['key' => 'value'],
        options: ['opt' => 'val'],
    );

    $json = $request->jsonSerialize();

    expect($json)->toHaveKeys(['customer', 'orderId', 'backLink', 'webhookUrl', 'customFields', 'options']);
    expect($json['customer']['email'])->toBe('test@example.com');
    expect($json['orderId'])->toBe('ord_123');
});

it('does not include null fields in JSON', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePaymentRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
    );

    $json = $request->jsonSerialize();

    expect($json)->not->toHaveKey('customer');
    expect($json)->not->toHaveKey('orderId');
    expect($json)->not->toHaveKey('backLink');
});

it('is immutable', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePaymentRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
    );
    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())->toBeTrue();
});
