<?php

declare(strict_types=1);

namespace LionTech\SDK;

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

final class LionTechSDK
{
    private readonly HttpClient $httpClient;

    private readonly HttpClient $secureHttpClient;

    private ?AuthClient $authClient = null;

    private ?OrdersClient $ordersClient = null;

    private ?PaymentsClient $paymentsClient = null;

    private ?RefundsClient $refundsClient = null;

    private ?PayoutsClient $payoutsClient = null;

    private ?TokensClient $tokensClient = null;

    private ?BalancesClient $balancesClient = null;

    private ?TransfersClient $transfersClient = null;

    private ?SignatureClient $signatureClient = null;

    /**
     * @param array{
     *     access_token?: string,
     *     refresh_token?: string,
     *     base_url?: string,
     *     secure_url?: string,
     *     client?: \Psr\Http\Client\ClientInterface,
     *     request_factory?: \Psr\Http\Message\RequestFactoryInterface,
     *     stream_factory?: \Psr\Http\Message\StreamFactoryInterface,
     * } $config
     */
    public function __construct(array $config = [])
    {
        $baseUrl = $config['base_url'] ?? 'https://api.liontechnology.ai';
        $secureUrl = $config['secure_url'] ?? 'https://secure.liontechnology.ai';
        $client = $config['client'] ?? null;
        $requestFactory = $config['request_factory'] ?? null;
        $streamFactory = $config['stream_factory'] ?? null;

        $this->httpClient = new HttpClient(
            baseUrl: $baseUrl,
            client: $client,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
        );

        $this->secureHttpClient = new HttpClient(
            baseUrl: $secureUrl,
            client: $client,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
        );

        if (isset($config['access_token'])) {
            $this->httpClient->setAccessToken($config['access_token']);
        }
    }

    public function auth(): AuthClient
    {
        return $this->authClient ??= new AuthClient($this->httpClient);
    }

    public function orders(): OrdersClient
    {
        return $this->ordersClient ??= new OrdersClient($this->httpClient);
    }

    public function payments(): PaymentsClient
    {
        return $this->paymentsClient ??= new PaymentsClient($this->httpClient);
    }

    public function refunds(): RefundsClient
    {
        return $this->refundsClient ??= new RefundsClient($this->httpClient);
    }

    public function payouts(): PayoutsClient
    {
        return $this->payoutsClient ??= new PayoutsClient($this->httpClient);
    }

    public function tokens(): TokensClient
    {
        return $this->tokensClient ??= new TokensClient($this->httpClient);
    }

    public function balances(): BalancesClient
    {
        return $this->balancesClient ??= new BalancesClient($this->httpClient);
    }

    public function transfers(): TransfersClient
    {
        return $this->transfersClient ??= new TransfersClient($this->httpClient);
    }

    public function signature(): SignatureClient
    {
        return $this->signatureClient ??= new SignatureClient($this->secureHttpClient);
    }

    /**
     * Create a webhook signature verifier.
     * Fetches the public key if not provided.
     */
    public function webhookVerifier(?string $publicKeyPem = null): WebhookSignatureVerifier
    {
        $pem = $publicKeyPem ?? $this->signature()
            ->getPublicKey();

        return new WebhookSignatureVerifier($pem);
    }

    /**
     * Create a card encryptor.
     * Fetches the encryption key if not provided.
     */
    public function cardEncryptor(?string $publicKeyPem = null): CardEncryptor
    {
        $pem = $publicKeyPem ?? $this->signature()
            ->getPublicKey();

        return new CardEncryptor($pem);
    }

    /**
     * Get the underlying HTTP client for advanced usage.
     */
    public function httpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Get the secure HTTP client for encryption/signature endpoints.
     */
    public function secureHttpClient(): HttpClient
    {
        return $this->secureHttpClient;
    }
}
