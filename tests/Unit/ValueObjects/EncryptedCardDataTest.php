<?php

declare(strict_types=1);

use LionTech\SDK\ValueObjects\EncryptedCardData;

it('creates encrypted card data', function (): void {
    $data = new EncryptedCardData(
        encryptedCardData: 'base64encodeddata',
        cardHolder: 'John Doe',
    );

    expect($data->encryptedCardData)->toBe('base64encodeddata');
    expect($data->cardHolder)->toBe('John Doe');
});

it('creates encrypted card data without card holder', function (): void {
    $data = new EncryptedCardData(encryptedCardData: 'base64encodeddata');

    expect($data->encryptedCardData)->toBe('base64encodeddata');
    expect($data->cardHolder)->toBeNull();
});

it('serializes to JSON with card holder', function (): void {
    $data = new EncryptedCardData(
        encryptedCardData: 'base64encodeddata',
        cardHolder: 'John Doe',
    );

    expect($data->jsonSerialize())->toBe([
        'encryptedCardData' => 'base64encodeddata',
        'cardHolder' => 'John Doe',
    ]);
});

it('serializes to JSON without card holder', function (): void {
    $data = new EncryptedCardData(encryptedCardData: 'base64encodeddata');

    expect($data->jsonSerialize())->toBe([
        'encryptedCardData' => 'base64encodeddata',
    ]);
});

it('is immutable', function (): void {
    $data = new EncryptedCardData(encryptedCardData: 'test');
    $reflection = new ReflectionClass($data);

    expect($reflection->isReadOnly())->toBeTrue();
});
