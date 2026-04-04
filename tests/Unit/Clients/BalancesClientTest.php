<?php

declare(strict_types=1);

use LionTech\SDK\Clients\BalancesClient;
use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\Http\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createBalancesClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $balancesClient = new BalancesClient($httpClient);

    return [$client, $requestFactory, $httpClient, $balancesClient];
}

function mockBalancesResponse($client, $requestFactory, string $jsonBody): void
{
    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/api/v1/merchant/balances')
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

it('lists balances', function (): void {
    [$client, $requestFactory, $httpClient, $balancesClient] = createBalancesClientMock();

    mockBalancesResponse($client, $requestFactory, json_encode([
        'items' => [
            [
                'accountId' => 'acc_123',
                'accountTypeId' => 'type_1',
                'mstId' => 'mst_1',
                'currency' => 'USD',
                'balance' => '1000.00',
                'createdAt' => '2024-01-01T00:00:00Z',
                'updatedAt' => '2024-01-02T00:00:00Z',
                'validOn' => '2024-01-01T00:00:00Z',
            ],
        ],
    ]));

    $result = $balancesClient->list();

    expect($result)
        ->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(MerchantAccount::class);
    expect($result[0]->accountId)->toBe('acc_123');
    expect($result[0]->balance)->toBe('1000.00');
});

it('lists balances with accounts key', function (): void {
    [$client, $requestFactory, $httpClient, $balancesClient] = createBalancesClientMock();

    mockBalancesResponse($client, $requestFactory, json_encode([
        'accounts' => [
            [
                'accountId' => 'acc_456',
                'accountTypeId' => 'type_2',
                'mstId' => 'mst_2',
                'currency' => 'EUR',
                'balance' => '500.00',
                'createdAt' => '2024-01-01T00:00:00Z',
                'updatedAt' => '2024-01-02T00:00:00Z',
                'validOn' => '2024-01-01T00:00:00Z',
            ],
        ],
    ]));

    $result = $balancesClient->list();

    expect($result)
        ->toHaveCount(1);
    expect($result[0]->currency->value)->toBe('EUR');
});

it('returns empty array when no balances', function (): void {
    [$client, $requestFactory, $httpClient, $balancesClient] = createBalancesClientMock();

    mockBalancesResponse($client, $requestFactory, json_encode([]));

    $result = $balancesClient->list();

    expect($result)
        ->toBe([]);
});
