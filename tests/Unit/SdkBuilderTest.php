<?php

declare(strict_types=1);

use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\LionTechSDK;

it('builds with required fields', function (): void {
    $sdk = LionTechSDK::builder()
        ->accessToken('test_token')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('builds with refresh token', function (): void {
    $sdk = LionTechSDK::builder()
        ->accessToken('test_token')
        ->refreshToken('test_refresh')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('builds with custom HTTP client', function (): void {
    $httpClient = new HttpClient('https://custom.api.com');

    $sdk = LionTechSDK::builder()
        ->accessToken('test_token')
        ->httpClient($httpClient)
        ->build();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('builds with custom base URL', function (): void {
    $sdk = LionTechSDK::builder()
        ->accessToken('test_token')
        ->baseUrl('https://custom.api.com')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('builds with custom secure URL', function (): void {
    $sdk = LionTechSDK::builder()
        ->accessToken('test_token')
        ->secureUrl('https://custom.secure.com')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('builds sandbox configuration', function (): void {
    $sdk = LionTechSDK::builder()
        ->accessToken('test_token')
        ->sandbox()
        ->build();

    expect($sdk)
        ->toBeInstanceOf(LionTechSDK::class);
});

it('throws without access token', function (): void {
    LionTechSDK::builder()->build();
})->throws(\InvalidArgumentException::class, 'Access token is required');
