<?php

declare(strict_types=1);

use LionTech\SDK\Clients\SignatureClient;
use LionTech\SDK\Http\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createSignatureClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://secure.example.com', $client, $requestFactory);
    $signatureClient = new SignatureClient($httpClient);

    return [$client, $requestFactory, $httpClient, $signatureClient];
}

it('gets the public key', function (): void {
    [$client, $requestFactory, $httpClient, $signatureClient] = createSignatureClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://secure.example.com/signature-key')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            'pem' => '-----BEGIN PUBLIC KEY-----test-----END PUBLIC KEY-----',
        ]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $signatureClient->getPublicKey();

    expect($result)
        ->toBe('-----BEGIN PUBLIC KEY-----test-----END PUBLIC KEY-----');
});
