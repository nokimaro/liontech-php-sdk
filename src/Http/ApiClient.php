<?php

declare(strict_types=1);

namespace LionTech\SDK\Http;

use LionTech\SDK\DTOs\Response\MerchantTokensRefreshResponse;
use LionTech\SDK\Exceptions\Auth\AuthenticationException;
use LionTech\SDK\Exceptions\Auth\TokenExpiredException;
use LionTech\SDK\Json;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private readonly HttpClient $merchantClient;

    private readonly HttpClient $secureClient;

    private readonly TokenStore $tokenStore;

    private function __construct(string $accessToken, ?string $refreshToken, HttpClient $merchant, HttpClient $secure)
    {
        $this->merchantClient = $merchant;
        $this->secureClient = $secure;
        $this->tokenStore = new TokenStore($accessToken, $refreshToken);
    }

    public static function create(
        string $accessToken,
        ?string $refreshToken = null,
        ?string $baseUrl = null,
        ?string $secureUrl = null,
        ?HttpClient $httpClient = null
    ): self {
        $base = $httpClient ?? new HttpClient($baseUrl ?? 'https://api.liontechnology.ai');
        $client = $base->client();
        $rf = $base->requestFactory();
        $sf = $base->streamFactory();
        $merchant = new HttpClient(
            baseUrl: $baseUrl ?? 'https://api.liontechnology.ai',
            client: $client,
            requestFactory: $rf,
            streamFactory: $sf
        );
        $secure = new HttpClient(
            baseUrl: $secureUrl ?? 'https://secure.liontechnology.ai',
            client: $client,
            requestFactory: $rf,
            streamFactory: $sf
        );
        return new self($accessToken, $refreshToken, $merchant, $secure);
    }

    public function merchantClient(): HttpClient
    {
        return $this->merchantClient;
    }

    public function secureClient(): HttpClient
    {
        return $this->secureClient;
    }

    public function tokenStore(): TokenStore
    {
        return $this->tokenStore;
    }

    /**
     * @param array<string, string|int|float|bool|null> $query
     */
    public function get(string $path, array $query = []): ResponseInterface
    {
        return $this->withRetry(fn (): ResponseInterface => $this->merchantClient->get($path, $query));
    }

    public function post(string $path, mixed $data = null): ResponseInterface
    {
        return $this->withRetry(fn (): ResponseInterface => $this->merchantClient->post($path, $data));
    }

    public function put(string $path, mixed $data = null): ResponseInterface
    {
        return $this->withRetry(fn (): ResponseInterface => $this->merchantClient->put($path, $data));
    }

    /**
     * @param array<string, string> $query
     */
    public function delete(string $path, array $query = []): ResponseInterface
    {
        return $this->withRetry(fn (): ResponseInterface => $this->merchantClient->delete($path, $query));
    }

    public function refreshTokens(): MerchantTokensRefreshResponse
    {
        $refresh = $this->tokenStore->refreshToken();
        if ($refresh === null) {
            throw new AuthenticationException('No refresh token available');
        }
        $req = new \LionTech\SDK\DTOs\Request\RefreshTokenRequest($refresh);
        $resp = $this->secureClient->post('/api/v1/merchant/auth/tokens/refresh', $req);
        $tokens = MerchantTokensRefreshResponse::fromArray(Json::decode((string) $resp->getBody()));
        $this->tokenStore->update($tokens->accessToken, $tokens->refreshToken);
        $this->merchantClient->setAccessToken($tokens->accessToken);
        return $tokens;
    }

    private function withRetry(callable $fn): ResponseInterface
    {
        $this->merchantClient->setAccessToken($this->tokenStore->accessToken());

        try {
            $result = $fn();
            assert($result instanceof ResponseInterface);

            return $result;
        } catch (TokenExpiredException $e) {
            if ($this->tokenStore->refreshToken() === null) {
                throw $e;
            }
            $this->refreshTokens();
            $result = $fn();
            assert($result instanceof ResponseInterface);

            return $result;
        }
    }
}
