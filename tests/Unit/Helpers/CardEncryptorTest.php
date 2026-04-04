<?php

declare(strict_types=1);

use LionTech\SDK\Helpers\CardEncryptor;
use phpseclib3\Crypt\RSA;

beforeEach(function (): void {
    // Generate a real RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $this->publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    // Test card data
    $this->cardData = [
        'pan' => '4405639704015096',
        'cvv' => '123',
        'exp_month' => 12,
        'exp_year' => 2030,
    ];

    $this->cardDataWithHolder = array_merge($this->cardData, [
        'cardHolder' => 'John Doe',
    ]);
});

it('creates CardEncryptor with public key', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    expect($encryptor)
        ->toBeInstanceOf(CardEncryptor::class);
});

it('encrypts card data successfully', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    $encrypted = $encryptor->encrypt($this->cardData);

    expect($encrypted)
        ->toBeString();
    expect($encrypted)
        ->not->toBeEmpty();
    // Base64 encoded encrypted data should be longer than original
    expect(strlen($encrypted))
        ->toBeGreaterThan(100);
});

it('returns valid base64 output', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    $encrypted = $encryptor->encrypt($this->cardData);

    // Should be valid base64
    expect(base64_decode($encrypted, true))
        ->not->toBeFalse();
});

it('encrypts card data for payment with card holder', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    $result = $encryptor->encryptForPayment($this->cardDataWithHolder);

    expect($result)
        ->toHaveKeys(['encryptedCardData', 'cardHolder']);
    expect($result['encryptedCardData'])->toBeString();
    expect($result['cardHolder'])->toBe('John Doe');
});

it('encrypts card data for payment without card holder', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    $result = $encryptor->encryptForPayment($this->cardData);

    expect($result)
        ->toHaveKeys(['encryptedCardData']);
    expect($result['cardHolder'] ?? null)->toBeNull();
});

it('produces different encryption results for same data', function (): void {
    // RSA-OAEP uses random padding, so same plaintext produces different ciphertext
    $encryptor = new CardEncryptor($this->publicKeyPem);

    $encrypted1 = $encryptor->encrypt($this->cardData);
    $encrypted2 = $encryptor->encrypt($this->cardData);

    expect($encrypted1)
        ->not->toBe($encrypted2);
});

it('handles different card data formats', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    // Test with different PAN
    $differentCard = [
        'pan' => '5522042705066736',
        'cvv' => '456',
        'exp_month' => 1,
        'exp_year' => 2025,
    ];

    $encrypted = $encryptor->encrypt($differentCard);

    expect($encrypted)
        ->toBeString();
    expect(strlen($encrypted))
        ->toBeGreaterThan(100);
});

it('is immutable', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);
    $reflection = new ReflectionClass($encryptor);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
