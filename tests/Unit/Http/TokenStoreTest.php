<?php

declare(strict_types=1);

use Nokimaro\LionTech\Http\TokenStore;

it('stores and returns tokens', function (): void {
    $store = new TokenStore('access_123', 'refresh_456');

    expect($store->accessToken())
        ->toBe('access_123');
    expect($store->refreshToken())
        ->toBe('refresh_456');
});

it('works without refresh token', function (): void {
    $store = new TokenStore('access_123');

    expect($store->accessToken())
        ->toBe('access_123');
    expect($store->refreshToken())
        ->toBeNull();
});

it('updates tokens', function (): void {
    $store = new TokenStore('old_access', 'old_refresh');
    $store->update('new_access', 'new_refresh');

    expect($store->accessToken())
        ->toBe('new_access');
    expect($store->refreshToken())
        ->toBe('new_refresh');
});
