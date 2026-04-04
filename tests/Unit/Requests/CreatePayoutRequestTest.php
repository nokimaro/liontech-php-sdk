<?php

declare(strict_types=1);

use Nokimaro\LionTech\Requests\CreatePayoutRequest;
use Nokimaro\LionTech\Requests\CustomerData;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\EncryptedCardData;
use Nokimaro\LionTech\ValueObjects\Money;
use Nokimaro\LionTech\ValueObjects\PaymentData;

it('creates payout request with minimal data', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePayoutRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);

    expect($request->amount->amount)
        ->toBe('100.00');
    expect($request->customer)
        ->toBeNull();
    expect($request->orderId)
        ->toBeNull();
});

it('creates payout request with all data', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePayoutRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
        customer: new CustomerData(email: 'test@example.com'),
        orderId: 'ord_123',
        webhookUrl: 'https://example.com/webhook',
        customFields: [
            'payout_ref' => 'ref_123',
        ],
    );

    expect($request->customer)
        ->not->toBeNull();
    expect($request->orderId)
        ->toBe('ord_123');
    expect($request->webhookUrl)
        ->toBe('https://example.com/webhook');
    expect($request->customFields)
        ->toBe([
            'payout_ref' => 'ref_123',
        ]);
});

it('serializes to JSON', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePayoutRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKeys(['amount', 'paymentData']);
    expect($json['amount'])->toBe([
        'value' => '100.00',
        'currency' => 'USD',
    ]);
});

it('serializes optional fields when present', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePayoutRequest(
        amount: new Money('100.00', Currency::USD),
        paymentData: $paymentData,
        customer: new CustomerData(email: 'test@example.com'),
        orderId: 'ord_123',
        webhookUrl: 'https://example.com/webhook',
        customFields: [
            'key' => 'value',
        ],
    );

    $json = $request->jsonSerialize();

    expect($json)
        ->toHaveKeys(['customer', 'orderId', 'webhookUrl', 'customFields']);
    expect($json['customer']['email'])->toBe('test@example.com');
});

it('does not include null fields in JSON', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePayoutRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);

    $json = $request->jsonSerialize();

    expect($json)
        ->not->toHaveKey('customer');
    expect($json)
        ->not->toHaveKey('orderId');
    expect($json)
        ->not->toHaveKey('webhookUrl');
});

it('is immutable', function (): void {
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted'));
    $request = new CreatePayoutRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);
    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
