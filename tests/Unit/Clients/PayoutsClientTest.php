<?php

declare(strict_types=1);

use LionTech\SDK\Clients\PayoutsClient;
use LionTech\SDK\DTOs\Request\CreatePayoutRequest;
use LionTech\SDK\DTOs\Response\PayoutResponse;
use LionTech\SDK\Enums\PaymentMethodType;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\EncryptedCardData;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createPayoutsClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $payoutsClient = new PayoutsClient($httpClient);

    return [$client, $requestFactory, $httpClient, $payoutsClient];
}

it('creates a payout with merchant id', function (): void {
    [$client, $requestFactory, $httpClient, $payoutsClient] = createPayoutsClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('PUT', 'https://api.example.com/api/v1/merchant/payouts/payout_123')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            'payoutId' => 'payout_123',
            'amount' => ['value' => '500.00', 'currency' => 'USD'],
            'status' => 'PENDING',
            'createdAt' => '2024-01-01T00:00:00Z',
        ]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted_data'));
    $payoutRequest = new CreatePayoutRequest(
        amount: new Money('500.00', Currency::USD),
        paymentData: $paymentData,
    );
    $result = $payoutsClient->createWithId('payout_123', $payoutRequest);

    expect($result)->toBeInstanceOf(PayoutResponse::class);
    expect($result->payoutId)->toBe('payout_123');
    expect($result->status->value)->toBe('PENDING');
});

it('gets a payout', function (): void {
    [$client, $requestFactory, $httpClient, $payoutsClient] = createPayoutsClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/api/v1/merchant/payouts/payout_123')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            'payoutId' => 'payout_123',
            'orderId' => 'ord_123',
            'amount' => ['value' => '500.00', 'currency' => 'USD'],
            'status' => 'SUCCEEDED',
            'createdAt' => '2024-01-01T00:00:00Z',
            'txnId' => 'txn_123',
            'rrn' => 'rrn_123',
        ]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $payoutsClient->get('payout_123');

    expect($result)->toBeInstanceOf(PayoutResponse::class);
    expect($result->payoutId)->toBe('payout_123');
    expect($result->orderId)->toBe('ord_123');
    expect($result->status->value)->toBe('SUCCEEDED');
    expect($result->txnId)->toBe('txn_123');
    expect($result->rrn)->toBe('rrn_123');
});

it('checks payout is final', function (): void {
    [$client, $requestFactory, $httpClient, $payoutsClient] = createPayoutsClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/api/v1/merchant/payouts/payout_456')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            'payoutId' => 'payout_456',
            'amount' => ['value' => '500.00', 'currency' => 'USD'],
            'status' => 'SUCCEEDED',
            'createdAt' => '2024-01-01T00:00:00Z',
        ]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $payoutsClient->get('payout_456');

    expect($result->isFinal())->toBeTrue();
    expect($result->isSuccessful())->toBeTrue();
});
