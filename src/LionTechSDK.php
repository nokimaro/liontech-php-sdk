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
use LionTech\SDK\Http\ApiClient;
use LionTech\SDK\Http\HttpClient;

final class LionTechSDK
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

    private ?CardEncryptor $cardEncryptor = null;

    private ?WebhookSignatureVerifier $webhookVerifier = null;

    public function __construct(
        string $accessToken,
        ?string $refreshToken = null,
        ?HttpClient $httpClient = null,
        ?string $baseUrl = null,
        ?string $secureUrl = null,
    ) {
        $this->apiClient = ApiClient::create(
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            baseUrl: $baseUrl ?? 'https://api.liontechnology.ai',
            secureUrl: $secureUrl ?? 'https://secure.liontechnology.ai',
            httpClient: $httpClient,
        );
    }

    public static function builder(): SdkBuilder
    {
        return new SdkBuilder();
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
        return $this->signatureClient ??= new SignatureClient($this->apiClient->secureClient());
    }

    public function cardEncryptor(?string $publicKeyPem = null): CardEncryptor
    {
        if ($this->cardEncryptor instanceof \LionTech\SDK\Helpers\CardEncryptor) {
            return $this->cardEncryptor;
        }

        $pem = $publicKeyPem ?? $this->signature()
            ->getPublicKey();

        return $this->cardEncryptor = new CardEncryptor($pem);
    }

    public function webhookVerifier(?string $publicKeyPem = null): WebhookSignatureVerifier
    {
        if ($this->webhookVerifier instanceof \LionTech\SDK\Helpers\WebhookSignatureVerifier) {
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
