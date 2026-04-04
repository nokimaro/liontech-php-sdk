<?php

declare(strict_types=1);

use LionTech\SDK\Clients\TransfersClient;
use LionTech\SDK\Http\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createTransfersClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $transfersClient = new TransfersClient($httpClient);

    return [$client, $requestFactory, $httpClient, $transfersClient];
}

it('creates a transfer', function (): void {
    [$client, $requestFactory, $httpClient, $transfersClient] = createTransfersClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('POST', 'https://api.example.com/api/v1/merchant/transfers')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode(['transferId' => 'tr_123', 'status' => 'completed']));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $transfersClient->create(['amount' => 100.00, 'currency' => 'USD']);

    expect($result)->toBe(['transferId' => 'tr_123', 'status' => 'completed']);
});

it('gets a transfer', function (): void {
    [$client, $requestFactory, $httpClient, $transfersClient] = createTransfersClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/api/v1/merchant/transfers/tr_123')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode(['transferId' => 'tr_123', 'status' => 'completed', 'amount' => 100.00]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $transfersClient->get('tr_123');

    expect($result['transferId'])->toBe('tr_123');
    expect($result['status'])->toBe('completed');
    expect($result['amount'])->toBe(100);
});
