<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Response\MerchantTokensRefreshResponse;

it('creates merchant tokens refresh response from array', function (): void {
    $data = [
        'accessToken' => 'new_access_token',
        'accessTokenExpireAt' => '2024-12-31T23:59:59Z',
        'refreshToken' => 'new_refresh_token',
        'refreshTokenExpireAt' => '2025-12-31T23:59:59Z',
    ];

    $response = MerchantTokensRefreshResponse::fromArray($data);

    expect($response->accessToken)
        ->toBe('new_access_token');
    expect($response->accessTokenExpireAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
    expect($response->refreshToken)
        ->toBe('new_refresh_token');
    expect($response->refreshTokenExpireAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
});

it('parses dates correctly', function (): void {
    $data = [
        'accessToken' => 'token',
        'accessTokenExpireAt' => '2024-06-15T10:30:00Z',
        'refreshToken' => 'refresh',
        'refreshTokenExpireAt' => '2025-06-15T10:30:00Z',
    ];

    $response = MerchantTokensRefreshResponse::fromArray($data);

    expect($response->accessTokenExpireAt->format('Y-m-d'))
        ->toBe('2024-06-15');
    expect($response->refreshTokenExpireAt->format('Y-m-d'))
        ->toBe('2025-06-15');
});

it('is immutable', function (): void {
    $data = [
        'accessToken' => 'token',
        'accessTokenExpireAt' => '2024-12-31T23:59:59Z',
        'refreshToken' => 'refresh',
        'refreshTokenExpireAt' => '2025-12-31T23:59:59Z',
    ];

    $response = MerchantTokensRefreshResponse::fromArray($data);
    $reflection = new ReflectionClass($response);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
