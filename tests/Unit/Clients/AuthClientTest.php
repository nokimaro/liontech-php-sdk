<?php

declare(strict_types=1);

use LionTech\SDK\Clients\AuthClient;
use LionTech\SDK\DTOs\Request\RefreshTokenRequest;
use LionTech\SDK\DTOs\Response\MerchantTokensRefreshResponse;
use LionTech\SDK\Http\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createAuthClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $authClient = new AuthClient($httpClient);

    return [$client, $requestFactory, $httpClient, $authClient];
}

it('refreshes tokens', function (): void {
    [$client, $requestFactory, $httpClient, $authClient] = createAuthClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('POST', 'https://api.example.com/api/v1/merchant/auth/tokens/refresh')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn('{"accessToken":"new_token","accessTokenExpireAt":"2024-12-31T23:59:59Z","refreshToken":"new_refresh","refreshTokenExpireAt":"2025-12-31T23:59:59Z"}');
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $refreshRequest = new RefreshTokenRequest('old_refresh');
    $result = $authClient->refreshTokens($refreshRequest);

    expect($result)
        ->toBeInstanceOf(MerchantTokensRefreshResponse::class);
    expect($result->accessToken)
        ->toBe('new_token');
    expect($result->refreshToken)
        ->toBe('new_refresh');
});

it('refreshes tokens and applies to http client', function (): void {
    [$client, $requestFactory, $httpClient, $authClient] = createAuthClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('POST', 'https://api.example.com/api/v1/merchant/auth/tokens/refresh')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn('{"accessToken":"new_token","accessTokenExpireAt":"2024-12-31T23:59:59Z","refreshToken":"new_refresh","refreshTokenExpireAt":"2025-12-31T23:59:59Z"}');
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $refreshRequest = new RefreshTokenRequest('old_refresh');
    $result = $authClient->refreshAndApply($refreshRequest);

    expect($result->accessToken)
        ->toBe('new_token');
    expect($httpClient->getAccessToken())
        ->toBe('new_token');
});
