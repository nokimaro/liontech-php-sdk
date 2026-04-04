<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\SbpData;
use LionTech\SDK\Enums\PaymentMethodType;
use LionTech\SDK\ValueObjects\EncryptedCardData;
use LionTech\SDK\ValueObjects\PaymentData;

it('creates card payment data using factory', function (): void {
    $cardData = new EncryptedCardData(encryptedCardData: 'encrypted', cardHolder: 'John Doe');
    $paymentData = PaymentData::card($cardData);

    expect($paymentData->type)->toBe(PaymentMethodType::CARD);
    expect($paymentData->object)->toBe($cardData);
});

it('creates sbp payment data using factory', function (): void {
    $sbpData = new SbpData(bank: 'sberbank');
    $paymentData = PaymentData::sbp($sbpData);

    expect($paymentData->type)->toBe(PaymentMethodType::SBP);
    expect($paymentData->object)->toBe($sbpData);
});

it('serializes card payment data', function (): void {
    $cardData = new EncryptedCardData(encryptedCardData: 'encrypted', cardHolder: 'John Doe');
    $paymentData = PaymentData::card($cardData);

    $json = $paymentData->jsonSerialize();

    expect($json)->toBe([
        'type' => 'card',
        'object' => [
            'encryptedCardData' => 'encrypted',
            'cardHolder' => 'John Doe',
        ],
    ]);
});

it('serializes sbp payment data', function (): void {
    $sbpData = new SbpData(bank: 'sberbank');
    $paymentData = PaymentData::sbp($sbpData);

    $json = $paymentData->jsonSerialize();

    expect($json)->toBe([
        'type' => 'sbp',
        'object' => [
            'bank' => 'sberbank',
        ],
    ]);
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

    expect($paymentData->type)->toBe(PaymentMethodType::CARD);
    expect($paymentData->object)->toBeInstanceOf(EncryptedCardData::class);
    expect($paymentData->object->encryptedCardData)->toBe('encrypted');
    expect($paymentData->object->cardHolder)->toBe('John Doe');
});

it('creates from array with sbp type', function (): void {
    $data = [
        'type' => 'sbp',
        'object' => [
            'bank' => 'tinkoff',
        ],
    ];

    $paymentData = PaymentData::fromArray($data);

    expect($paymentData->type)->toBe(PaymentMethodType::SBP);
    expect($paymentData->object)->toBeInstanceOf(SbpData::class);
    expect($paymentData->object->bank)->toBe('tinkoff');
});

it('creates from array with type as object', function (): void {
    $data = [
        'type' => 'card',
        'object' => [
            'encryptedCardData' => 'encrypted',
        ],
    ];

    $paymentData = PaymentData::fromArray($data);

    expect($paymentData->type)->toBe(PaymentMethodType::CARD);
});

it('is immutable', function (): void {
    $cardData = new EncryptedCardData(encryptedCardData: 'encrypted');
    $paymentData = PaymentData::card($cardData);
    $reflection = new ReflectionClass($paymentData);

    expect($reflection->isReadOnly())->toBeTrue();
});
