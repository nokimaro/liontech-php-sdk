<?php

declare(strict_types=1);

use Nokimaro\LionTech\Client;
use Nokimaro\LionTech\Clients\AuthClient;
use Nokimaro\LionTech\Clients\BalancesClient;
use Nokimaro\LionTech\Clients\OrdersClient;
use Nokimaro\LionTech\Clients\PaymentsClient;
use Nokimaro\LionTech\Clients\PayoutsClient;
use Nokimaro\LionTech\Clients\RefundsClient;
use Nokimaro\LionTech\Clients\SignatureClient;
use Nokimaro\LionTech\Clients\TokensClient;
use Nokimaro\LionTech\Clients\TransfersClient;
use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Security\CardEncryptor;
use Nokimaro\LionTech\Security\WebhookSignatureVerifier;

it('creates with minimal config', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('creates with refresh token', function (): void {
    $sdk = new Client('test_access_token', 'test_refresh_token');

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('creates with custom base URL', function (): void {
    $sdk = new Client('test_access_token', baseUrl: 'https://custom.api.com');

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('returns same instance on repeated client access', function (): void {
    $sdk = new Client('test_access_token');

    $orders1 = $sdk->orders();
    $orders2 = $sdk->orders();

    expect($orders1)
        ->toBe($orders2);
});

it('creates auth client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->auth())
        ->toBeInstanceOf(AuthClient::class);
});

it('creates orders client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->orders())
        ->toBeInstanceOf(OrdersClient::class);
});

it('creates payments client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->payments())
        ->toBeInstanceOf(PaymentsClient::class);
});

it('creates refunds client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->refunds())
        ->toBeInstanceOf(RefundsClient::class);
});

it('creates payouts client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->payouts())
        ->toBeInstanceOf(PayoutsClient::class);
});

it('creates tokens client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->tokens())
        ->toBeInstanceOf(TokensClient::class);
});

it('creates balances client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->balances())
        ->toBeInstanceOf(BalancesClient::class);
});

it('creates transfers client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->transfers())
        ->toBeInstanceOf(TransfersClient::class);
});

it('creates signature client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->signature())
        ->toBeInstanceOf(SignatureClient::class);
});

it('returns api client', function (): void {
    $sdk = new Client('test_access_token');

    expect($sdk->apiClient())
        ->toBeInstanceOf(ApiClient::class);
});

it('creates card encryptor with provided key', function (): void {
    $privateKey = phpseclib3\Crypt\RSA::createKey(2048);
    $publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    $sdk = new Client('test_access_token');

    expect($sdk->cardEncryptor($publicKeyPem))
        ->toBeInstanceOf(CardEncryptor::class);
});

it('creates webhook verifier with provided key', function (): void {
    $privateKey = phpseclib3\Crypt\RSA::createKey(2048);
    $publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    $sdk = new Client('test_access_token');

    expect($sdk->webhookVerifier($publicKeyPem))
        ->toBeInstanceOf(WebhookSignatureVerifier::class);
});

it('caches card encryptor', function (): void {
    $privateKey = phpseclib3\Crypt\RSA::createKey(2048);
    $publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    $sdk = new Client('test_access_token');

    $encryptor1 = $sdk->cardEncryptor($publicKeyPem);
    $encryptor2 = $sdk->cardEncryptor();

    expect($encryptor1)
        ->toBe($encryptor2);
});

it('caches webhook verifier', function (): void {
    $privateKey = phpseclib3\Crypt\RSA::createKey(2048);
    $publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    $sdk = new Client('test_access_token');

    $verifier1 = $sdk->webhookVerifier($publicKeyPem);
    $verifier2 = $sdk->webhookVerifier();

    expect($verifier1)
        ->toBe($verifier2);
});
