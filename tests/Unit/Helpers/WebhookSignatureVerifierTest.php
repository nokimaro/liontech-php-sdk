<?php

declare(strict_types=1);

use LionTech\SDK\Helpers\WebhookSignatureVerifier;
use phpseclib3\Crypt\RSA;

it('can be instantiated with valid RSA public key', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    expect($verifier)
        ->toBeInstanceOf(WebhookSignatureVerifier::class);
});

it('verifies valid signature', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    $payload = 'test payload';
    $signature = $privateKey->sign($payload);
    $signatureBase64 = base64_encode($signature);

    $headers = [
        'X-Payload-Signature' => $signatureBase64,
    ];

    expect($verifier->verify($headers, $payload))
        ->toBeTrue();
});

it('rejects invalid signature', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    $headers = [
        'X-Payload-Signature' => base64_encode('invalid_signature'),
    ];

    expect($verifier->verify($headers, 'test payload'))
        ->toBeFalse();
});

it('returns false when signature header is missing', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    $headers = [
        'Content-Type' => 'application/json',
    ];

    expect($verifier->verify($headers, 'test payload'))
        ->toBeFalse();
});

it('returns false when signature is not valid base64', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    $headers = [
        'X-Payload-Signature' => '!!!invalid-base64!!!',
    ];

    expect($verifier->verify($headers, 'test payload'))
        ->toBeFalse();
});

it('handles signature header as array', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    $payload = 'test payload';
    $signature = $privateKey->sign($payload);
    $signatureBase64 = base64_encode($signature);

    $headers = [
        'X-Payload-Signature' => [$signatureBase64, 'another_value'],
    ];

    expect($verifier->verify($headers, $payload))
        ->toBeTrue();
});

it('returns false when signature header array has non-string first element', function (): void {
    // Generate a temporary RSA key pair for testing
    $privateKey = RSA::createKey(2048);
    $publicKey = $privateKey->getPublicKey();
    $publicKeyPem = $publicKey->toString('PKCS8');

    $verifier = new WebhookSignatureVerifier($publicKeyPem);

    $headers = [
        'X-Payload-Signature' => [123, 'another_value'],
    ];

    expect($verifier->verify($headers, 'test payload'))
        ->toBeFalse();
});
