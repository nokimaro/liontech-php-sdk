<?php

declare(strict_types=1);

use LionTech\SDK\Clients\TokensClient;
use LionTech\SDK\DTOs\Response\SavedPaymentMethod;
use LionTech\SDK\Http\ApiClient;

function createTokensClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $tokensClient = new TokensClient($apiClient);

    return [$apiClient, $tokensClient];
}

function tokenData(): array
{
    return [
        'saved_payment_methods' => [
            [
                'payment_method_id' => 'pm_456',
                'token_id' => 'tok_456',
                'display_value' => 'Mastercard ****5678',
                'card_type' => 'MASTERCARD',
                'card_exp' => '01/26',
                'card_requires_cvv' => false,
            ],
        ],
    ];
}

it('lists tokens by account id', function (): void {
    [$apiClient, $tokensClient] = createTokensClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/tokens', [
            'accountId' => 'acc_123',
        ])
        ->andReturn(mockResponse(tokenData()));

    $result = $tokensClient->list(accountId: 'acc_123');

    expect($result)
        ->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(SavedPaymentMethod::class);
    expect($result[0]->tokenId)->toBe('tok_456');
});

it('lists tokens by email', function (): void {
    [$apiClient, $tokensClient] = createTokensClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/tokens', [
            'email' => 'test@example.com',
        ])
        ->andReturn(mockResponse([
            'saved_payment_methods' => [],
        ]));

    $result = $tokensClient->list(email: 'test@example.com');

    expect($result)
        ->toBe([]);
});

it('lists tokens by phone', function (): void {
    [$apiClient, $tokensClient] = createTokensClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/tokens', [
            'phone' => '+1234567890',
        ])
        ->andReturn(mockResponse([
            'saved_payment_methods' => [],
        ]));

    $result = $tokensClient->list(phone: '+1234567890');

    expect($result)
        ->toBe([]);
});

it('deletes a token', function (): void {
    [$apiClient, $tokensClient] = createTokensClient();

    $apiClient->shouldReceive('delete')
        ->with('/api/v1/merchant/tokens/tok_123');

    $tokensClient->delete('tok_123');
});
