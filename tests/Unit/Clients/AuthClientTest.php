<?php

declare(strict_types=1);

use LionTech\SDK\Clients\AuthClient;
use LionTech\SDK\DTOs\Response\MerchantTokensRefreshResponse;
use LionTech\SDK\Http\ApiClient;

function createAuthClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $authClient = new AuthClient($apiClient);

    return [$apiClient, $authClient];
}

it('refreshes tokens', function (): void {
    [$apiClient, $authClient] = createAuthClient();

    $tokens = new MerchantTokensRefreshResponse(
        accessToken: 'new_access',
        accessTokenExpireAt: new DateTimeImmutable('2024-12-31T23:59:59Z'),
        refreshToken: 'new_refresh',
        refreshTokenExpireAt: new DateTimeImmutable('2025-12-31T23:59:59Z'),
    );

    $apiClient->shouldReceive('refreshTokens')
        ->andReturn($tokens);

    $result = $authClient->refreshTokens();

    expect($result)
        ->toBe($tokens);
    expect($result->accessToken)
        ->toBe('new_access');
    expect($result->refreshToken)
        ->toBe('new_refresh');
});

it('returns refresh token', function (): void {
    [$apiClient, $authClient] = createAuthClient();
    $tokenStore = Mockery::mock(\LionTech\SDK\Http\TokenStore::class);
    $tokenStore->shouldReceive('refreshToken')
        ->andReturn('refresh_token_123');
    $apiClient->shouldReceive('tokenStore')
        ->andReturn($tokenStore);

    $result = $authClient->refreshToken();

    expect($result)
        ->toBe('refresh_token_123');
});

it('throws when no refresh token configured', function (): void {
    [$apiClient, $authClient] = createAuthClient();
    $tokenStore = Mockery::mock(\LionTech\SDK\Http\TokenStore::class);
    $tokenStore->shouldReceive('refreshToken')
        ->andReturn(null);
    $apiClient->shouldReceive('tokenStore')
        ->andReturn($tokenStore);

    $authClient->refreshToken();
})->throws(\RuntimeException::class, 'No refresh token configured');
