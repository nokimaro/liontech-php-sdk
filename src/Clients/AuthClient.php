<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Responses\MerchantTokensRefreshResponse;

final readonly class AuthClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    public function refreshTokens(): MerchantTokensRefreshResponse
    {
        return $this->apiClient->refreshTokens();
    }

    public function refreshToken(): string
    {
        $token = $this->apiClient->tokenStore()
            ->refreshToken();

        if ($token === null) {
            throw new \RuntimeException('No refresh token configured');
        }

        return $token;
    }
}
