<?php

declare(strict_types=1);

use Nokimaro\LionTech\Client;
use Nokimaro\LionTech\Http\Transport;

it('builds with required fields', function (): void {
    $sdk = Client::builder()
        ->accessToken('test_token')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('builds with refresh token', function (): void {
    $sdk = Client::builder()
        ->accessToken('test_token')
        ->refreshToken('test_refresh')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('builds with custom HTTP client', function (): void {
    $httpClient = new Transport('https://custom.api.com');

    $sdk = Client::builder()
        ->accessToken('test_token')
        ->httpClient($httpClient)
        ->build();

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('builds with custom base URL', function (): void {
    $sdk = Client::builder()
        ->accessToken('test_token')
        ->baseUrl('https://custom.api.com')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('builds with custom secure URL', function (): void {
    $sdk = Client::builder()
        ->accessToken('test_token')
        ->secureUrl('https://custom.secure.com')
        ->build();

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('builds sandbox configuration', function (): void {
    $sdk = Client::builder()
        ->accessToken('test_token')
        ->sandbox()
        ->build();

    expect($sdk)
        ->toBeInstanceOf(Client::class);
});

it('throws without access token', function (): void {
    Client::builder()->build();
})->throws(\InvalidArgumentException::class, 'Access token is required');
