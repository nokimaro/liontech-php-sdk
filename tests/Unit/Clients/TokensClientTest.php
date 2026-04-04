<?php

declare(strict_types=1);

use LionTech\SDK\Clients\TokensClient;
use LionTech\SDK\DTOs\Response\SavedPaymentMethod;
use LionTech\SDK\Http\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createTokensClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $tokensClient = new TokensClient($httpClient);

    return [$client, $requestFactory, $httpClient, $tokensClient];
}

function mockTokensListResponse($requestFactory, $client, string $url, string $jsonBody): void
{
    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', $url)
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn($jsonBody);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);
}

it('lists saved payment methods', function (): void {
    [$client, $requestFactory, $httpClient, $tokensClient] = createTokensClientMock();

    mockTokensListResponse($requestFactory, $client, 'https://api.example.com/api/v1/merchant/tokens', json_encode([
        'saved_payment_methods' => [
            [
                'payment_method_id' => 'pm_123',
                'token_id' => 'tok_123',
                'display_value' => 'Visa ****1234',
                'card_type' => 'VISA',
                'card_exp' => '12/25',
                'card_requires_cvv' => true,
            ],
        ],
    ]));

    $result = $tokensClient->list();

    expect($result)->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(SavedPaymentMethod::class);
    expect($result[0]->paymentMethodId)->toBe('pm_123');
    expect($result[0]->tokenId)->toBe('tok_123');
    expect($result[0]->cardRequiresCvv)->toBeTrue();
});

it('lists saved payment methods with items key', function (): void {
    [$client, $requestFactory, $httpClient, $tokensClient] = createTokensClientMock();

    mockTokensListResponse($requestFactory, $client, 'https://api.example.com/api/v1/merchant/tokens?accountId=acc_123', json_encode([
        'items' => [
            [
                'payment_method_id' => 'pm_456',
                'token_id' => 'tok_456',
                'display_value' => 'Mastercard ****5678',
                'card_type' => 'MASTERCARD',
                'card_exp' => '01/26',
                'card_requires_cvv' => false,
            ],
        ],
    ]));

    $result = $tokensClient->list(accountId: 'acc_123');

    expect($result)->toHaveCount(1);
    expect($result[0]->paymentMethodId)->toBe('pm_456');
    expect($result[0]->cardRequiresCvv)->toBeFalse();
});

it('filters by email', function (): void {
    [$client, $requestFactory, $httpClient, $tokensClient] = createTokensClientMock();

    mockTokensListResponse($requestFactory, $client, 'https://api.example.com/api/v1/merchant/tokens?email=test%40example.com', json_encode([
        'saved_payment_methods' => [],
    ]));

    $result = $tokensClient->list(email: 'test@example.com');

    expect($result)->toBe([]);
});

it('filters by phone', function (): void {
    [$client, $requestFactory, $httpClient, $tokensClient] = createTokensClientMock();

    mockTokensListResponse($requestFactory, $client, 'https://api.example.com/api/v1/merchant/tokens?phone=%2B1234567890', json_encode([
        'saved_payment_methods' => [],
    ]));

    $result = $tokensClient->list(phone: '+1234567890');

    expect($result)->toBe([]);
});

it('deletes a token', function (): void {
    [$client, $requestFactory, $httpClient, $tokensClient] = createTokensClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('DELETE', 'https://api.example.com/api/v1/merchant/tokens/tok_123')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $stream->shouldReceive('__toString')
        ->andReturn('');
    $response->shouldReceive('getStatusCode')
        ->andReturn(204);

    $tokensClient->delete('tok_123');

    expect(true)->toBeTrue(); // Just verify no exception is thrown
});
