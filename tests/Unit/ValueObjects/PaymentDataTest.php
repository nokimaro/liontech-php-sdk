<?php

declare(strict_types=1);

use Nokimaro\LionTech\Enums\PaymentMethodType;
use Nokimaro\LionTech\ValueObjects\EncryptedCardData;
use Nokimaro\LionTech\ValueObjects\PaymentData;

it('creates card payment data using factory', function (): void {
    $cardData = new EncryptedCardData(encryptedCardData: 'encrypted', cardHolder: 'John Doe');
    $paymentData = PaymentData::card($cardData);

    expect($paymentData->type)
        ->toBe(PaymentMethodType::CARD);
    expect($paymentData->object)
        ->toBe($cardData);
});

it('creates sbp payment data using factory', function (): void {
    $paymentData = PaymentData::sbp();

    expect($paymentData->type)
        ->toBe(PaymentMethodType::SBP);
    expect($paymentData->object)
        ->toBeNull();
});

it('serializes card payment data', function (): void {
    $cardData = new EncryptedCardData(encryptedCardData: 'encrypted', cardHolder: 'John Doe');
    $paymentData = PaymentData::card($cardData);

    $json = $paymentData->jsonSerialize();

    expect($json)
        ->toBe([
            'type' => 'card',
            'object' => [
                'encryptedCardData' => 'encrypted',
                'cardHolder' => 'John Doe',
            ],
        ]);
});

it('serializes sbp payment data as empty object', function (): void {
    $paymentData = PaymentData::sbp();

    $json = json_encode($paymentData);

    expect($json)
        ->toBe('{"type":"sbp","object":{}}');
});

it('creates from array with card type', function (): void {
    $data = [
        'type' => 'card',
        'object' => [
            'encryptedCardData' => 'encrypted',
            'cardHolder' => 'John Doe',
        ],
    ];

    $paymentData = PaymentData::fromArray($data);

    expect($paymentData->type)
        ->toBe(PaymentMethodType::CARD);
    expect($paymentData->object)
        ->toBeInstanceOf(EncryptedCardData::class);
    expect($paymentData->object->encryptedCardData)
        ->toBe('encrypted');
    expect($paymentData->object->cardHolder)
        ->toBe('John Doe');
});

it('creates from array with sbp type', function (): void {
    $data = [
        'type' => 'sbp',
        'object' => [],
    ];

    $paymentData = PaymentData::fromArray($data);

    expect($paymentData->type)
        ->toBe(PaymentMethodType::SBP);
    expect($paymentData->object)
        ->toBeNull();
});

it('creates from array with type as object', function (): void {
    $data = [
        'type' => 'card',
        'object' => [
            'encryptedCardData' => 'encrypted',
        ],
    ];

    $paymentData = PaymentData::fromArray($data);

    expect($paymentData->type)
        ->toBe(PaymentMethodType::CARD);
});

it('is immutable', function (): void {
    $cardData = new EncryptedCardData(encryptedCardData: 'encrypted');
    $paymentData = PaymentData::card($cardData);
    $reflection = new ReflectionClass($paymentData);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
