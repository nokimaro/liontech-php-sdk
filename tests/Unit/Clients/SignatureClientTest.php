<?php

declare(strict_types=1);

use Nokimaro\LionTech\Clients\SignatureClient;
use Nokimaro\LionTech\Http\Transport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createSignatureClient(): array
{
    $httpClient = Mockery::mock(Transport::class);
    $signatureClient = new SignatureClient($httpClient);

    return [$httpClient, $signatureClient];
}

it('gets public key', function (): void {
    [$httpClient, $signatureClient] = createSignatureClient();

    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            'pem' => '-----BEGIN PUBLIC KEY-----...',
        ]));

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getBody')
        ->andReturn($stream);

    $httpClient->shouldReceive('get')
        ->with('/signature-key')
        ->andReturn($response);

    $key = $signatureClient->getPublicKey();

    expect($key)
        ->toBe('-----BEGIN PUBLIC KEY-----...');
});
