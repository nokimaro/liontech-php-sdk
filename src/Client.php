<?php

declare(strict_types=1);

namespace Nokimaro\LionTech;

use Nokimaro\LionTech\Clients\AuthClient;
use Nokimaro\LionTech\Clients\BalancesClient;
use Nokimaro\LionTech\Clients\EncryptionKeyClient;
use Nokimaro\LionTech\Clients\OrdersClient;
use Nokimaro\LionTech\Clients\PaymentsClient;
use Nokimaro\LionTech\Clients\PayoutsClient;
use Nokimaro\LionTech\Clients\RefundsClient;
use Nokimaro\LionTech\Clients\SignatureClient;
use Nokimaro\LionTech\Clients\TokensClient;
use Nokimaro\LionTech\Clients\TransfersClient;
use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Http\Transport;
use Nokimaro\LionTech\Security\CardEncryptor;
use Nokimaro\LionTech\Security\WebhookSignatureVerifier;

final class Client
{
    private readonly ApiClient $apiClient;

    private ?AuthClient $authClient = null;

    private ?OrdersClient $ordersClient = null;

    private ?PaymentsClient $paymentsClient = null;

    private ?RefundsClient $refundsClient = null;

    private ?PayoutsClient $payoutsClient = null;

    private ?TokensClient $tokensClient = null;

    private ?BalancesClient $balancesClient = null;

    private ?TransfersClient $transfersClient = null;

    private ?SignatureClient $signatureClient = null;

    private ?EncryptionKeyClient $encryptionKeyClient = null;

    private ?CardEncryptor $cardEncryptor = null;

    private ?WebhookSignatureVerifier $webhookVerifier = null;

    public function __construct(
        string $accessToken,
        ?string $refreshToken = null,
        ?Transport $httpClient = null,
        ?string $baseUrl = null,
        ?string $secureUrl = null,
    ) {
        $this->apiClient = ApiClient::create(
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            baseUrl: $baseUrl ?? 'https://api.fusionpayments.io',
            secureUrl: $secureUrl ?? 'https://secure.fusionpayments.io',
            httpClient: $httpClient,
        );
    }

    public static function builder(): ClientBuilder
    {
        return new ClientBuilder();
    }

    public function auth(): AuthClient
    {
        return $this->authClient ??= new AuthClient($this->apiClient);
    }

    public function orders(): OrdersClient
    {
        return $this->ordersClient ??= new OrdersClient($this->apiClient);
    }

    public function payments(): PaymentsClient
    {
        return $this->paymentsClient ??= new PaymentsClient($this->apiClient);
    }

    public function refunds(): RefundsClient
    {
        return $this->refundsClient ??= new RefundsClient($this->apiClient);
    }

    public function payouts(): PayoutsClient
    {
        return $this->payoutsClient ??= new PayoutsClient($this->apiClient);
    }

    public function tokens(): TokensClient
    {
        return $this->tokensClient ??= new TokensClient($this->apiClient);
    }

    public function balances(): BalancesClient
    {
        return $this->balancesClient ??= new BalancesClient($this->apiClient);
    }

    public function transfers(): TransfersClient
    {
        return $this->transfersClient ??= new TransfersClient($this->apiClient);
    }

    public function signature(): SignatureClient
    {
        return $this->signatureClient ??= new SignatureClient($this->apiClient->merchantClient());
    }

    public function encryptionKey(): EncryptionKeyClient
    {
        return $this->encryptionKeyClient ??= new EncryptionKeyClient($this->apiClient->secureClient());
    }

    public function cardEncryptor(?string $publicKeyPem = null): CardEncryptor
    {
        if ($this->cardEncryptor instanceof \Nokimaro\LionTech\Security\CardEncryptor) {
            return $this->cardEncryptor;
        }

        $pem = $publicKeyPem ?? $this->encryptionKey()
            ->getPublicKey();

        return $this->cardEncryptor = new CardEncryptor($pem);
    }

    public function webhookVerifier(?string $publicKeyPem = null): WebhookSignatureVerifier
    {
        if ($this->webhookVerifier instanceof \Nokimaro\LionTech\Security\WebhookSignatureVerifier) {
            return $this->webhookVerifier;
        }

        $pem = $publicKeyPem ?? $this->signature()
            ->getPublicKey();

        return $this->webhookVerifier = new WebhookSignatureVerifier($pem);
    }

    /**
     * Get the underlying API client for advanced usage.
     */
    public function apiClient(): ApiClient
    {
        return $this->apiClient;
    }
}
