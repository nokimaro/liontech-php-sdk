<?php

declare(strict_types=1);

use Nokimaro\LionTech\Responses\SavedPaymentMethod;

it('creates saved payment method from array', function (): void {
    $data = [
        'payment_method_id' => 'pm_123',
        'token_id' => 'tok_123',
        'display_value' => 'Visa ****1234',
        'card_type' => 'VISA',
        'card_exp' => '12/25',
        'card_requires_cvv' => true,
    ];

    $method = SavedPaymentMethod::fromArray($data);

    expect($method->paymentMethodId)
        ->toBe('pm_123');
    expect($method->tokenId)
        ->toBe('tok_123');
    expect($method->displayValue)
        ->toBe('Visa ****1234');
    expect($method->cardType)
        ->toBe('VISA');
    expect($method->cardExp)
        ->toBe('12/25');
    expect($method->cardRequiresCvv)
        ->toBeTrue();
});

it('parses card_requires_cvv as false', function (): void {
    $data = [
        'payment_method_id' => 'pm_456',
        'token_id' => 'tok_456',
        'display_value' => 'Mastercard ****5678',
        'card_type' => 'MASTERCARD',
        'card_exp' => '01/26',
        'card_requires_cvv' => false,
    ];

    $method = SavedPaymentMethod::fromArray($data);

    expect($method->cardRequiresCvv)
        ->toBeFalse();
});

it('is immutable', function (): void {
    $data = [
        'payment_method_id' => 'pm_123',
        'token_id' => 'tok_123',
        'display_value' => 'Visa ****1234',
        'card_type' => 'VISA',
        'card_exp' => '12/25',
        'card_requires_cvv' => true,
    ];

    $method = SavedPaymentMethod::fromArray($data);
    $reflection = new ReflectionClass($method);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
