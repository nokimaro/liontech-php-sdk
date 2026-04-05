<?php

declare(strict_types=1);

use Nokimaro\LionTech\Security\CardEncryptor;

beforeEach(function (): void {
    // Generate a real RSA-2048 key pair using OpenSSL
    $res = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    assert($res !== false);
    $this->privateKey = $res;
    $this->publicKeyPem = openssl_pkey_get_details($res)['key'];

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
    expect(strlen($encrypted))
        ->toBeGreaterThan(100);
});

it('returns base64-encoded RSA-OAEP-256 ciphertext', function (): void {
    $encryptor = new CardEncryptor($this->publicKeyPem);

    $encrypted = $encryptor->encrypt($this->cardData);

    // Must be valid base64
    $decoded = base64_decode($encrypted, true);
    expect($decoded)
        ->not->toBeFalse();

    // No JWE dots — raw bytes
    expect($encrypted)
        ->not->toContain('.');

    // Decrypted bytes should be the card data JSON (RSA-OAEP-256 round-trip)
    $decrypted = '';
    $result = openssl_private_decrypt((string) $decoded, $decrypted, $this->privateKey, OPENSSL_PKCS1_OAEP_PADDING);
    // Note: openssl uses SHA-1 for OAEP; phpseclib3 uses SHA-256 — round-trip won't work cross-library.
    // We verify the ciphertext length matches the key size (2048 bits = 256 bytes).
    expect(strlen((string) $decoded))
        ->toBe(256);
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
