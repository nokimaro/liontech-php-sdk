<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\RefreshTokenRequest;

it('creates refresh token request', function (): void {
    $request = new RefreshTokenRequest('refresh_token_123');

    expect($request->refreshToken)
        ->toBe('refresh_token_123');
});

it('serializes to JSON', function (): void {
    $request = new RefreshTokenRequest('refresh_token_123');

    $json = $request->jsonSerialize();

    expect($json)
        ->toBe([
            'refreshToken' => 'refresh_token_123',
        ]);
});

it('is immutable', function (): void {
    $request = new RefreshTokenRequest('refresh_token_123');
    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
