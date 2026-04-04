<?php

declare(strict_types=1);

use LionTech\SDK\Clients\AuthClient;
use LionTech\SDK\Clients\BalancesClient;
use LionTech\SDK\Clients\OrdersClient;
use LionTech\SDK\Clients\PaymentsClient;
use LionTech\SDK\Clients\PayoutsClient;
use LionTech\SDK\Clients\RefundsClient;
use LionTech\SDK\Clients\SignatureClient;
use LionTech\SDK\Clients\TokensClient;
use LionTech\SDK\Clients\TransfersClient;
use LionTech\SDK\Helpers\CardEncryptor;
use LionTech\SDK\Helpers\WebhookSignatureVerifier;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\LionTechSDK;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

it('creates with default config', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('creates with custom config', function (): void {
    $httpClient = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $sdk = new LionTechSDK([
        'access_token' => 'test_token',
        'base_url' => 'https://custom.api.example.com',
        'secure_url' => 'https://custom.secure.example.com',
        'client' => $httpClient,
        'request_factory' => $requestFactory,
        'stream_factory' => $streamFactory,
    ]);

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('returns same instance on repeated client access', function (): void {
    $sdk = new LionTechSDK();

    $orders1 = $sdk->orders();
    $orders2 = $sdk->orders();

    expect($orders1)
        ->toBe($orders2);
});

it('creates auth client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->auth())
        ->toBeInstanceOf(AuthClient::class);
});

it('creates orders client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->orders())
        ->toBeInstanceOf(OrdersClient::class);
});

it('creates payments client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->payments())
        ->toBeInstanceOf(PaymentsClient::class);
});

it('creates refunds client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->refunds())
        ->toBeInstanceOf(RefundsClient::class);
});

it('creates payouts client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->payouts())
        ->toBeInstanceOf(PayoutsClient::class);
});

it('creates tokens client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->tokens())
        ->toBeInstanceOf(TokensClient::class);
});

it('creates balances client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->balances())
        ->toBeInstanceOf(BalancesClient::class);
});

it('creates transfers client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->transfers())
        ->toBeInstanceOf(TransfersClient::class);
});

it('creates signature client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->signature())
        ->toBeInstanceOf(SignatureClient::class);
});

it('returns http client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->httpClient())
        ->toBeInstanceOf(HttpClient::class);
});

it('returns secure http client', function (): void {
    $sdk = new LionTechSDK();

    expect($sdk->secureHttpClient())
        ->toBeInstanceOf(HttpClient::class);
});

it('creates webhook verifier with provided key', function (): void {
    $privateKey = phpseclib3\Crypt\RSA::createKey(2048);
    $publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    $sdk = new LionTechSDK();

    expect($sdk->webhookVerifier($publicKeyPem))
        ->toBeInstanceOf(WebhookSignatureVerifier::class);
});

it('creates card encryptor with provided key', function (): void {
    $privateKey = phpseclib3\Crypt\RSA::createKey(2048);
    $publicKeyPem = $privateKey->getPublicKey()
        ->toString('PKCS8');

    $sdk = new LionTechSDK();

    expect($sdk->cardEncryptor($publicKeyPem))
        ->toBeInstanceOf(CardEncryptor::class);
});
