<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\RefreshTokenRequest;
use LionTech\SDK\DTOs\Response\MerchantTokensRefreshResponse;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class AuthClient
{
    private const string TOKEN_REFRESH_PATH = '/api/v1/merchant/auth/tokens/refresh';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * Refresh access and refresh tokens.
     */
    public function refreshTokens(RefreshTokenRequest $request): MerchantTokensRefreshResponse
    {
        $response = $this->httpClient->post(self::TOKEN_REFRESH_PATH, $request);
        $data = Json::decode((string) $response->getBody());

        return MerchantTokensRefreshResponse::fromArray($data);
    }

    /**
     * Refresh tokens and update the HTTP client's access token.
     */
    public function refreshAndApply(RefreshTokenRequest $request): MerchantTokensRefreshResponse
    {
        $response = $this->refreshTokens($request);
        $this->httpClient->setAccessToken($response->accessToken);

        return $response;
    }
}
