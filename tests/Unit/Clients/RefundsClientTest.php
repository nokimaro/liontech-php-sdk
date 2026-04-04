<?php

declare(strict_types=1);

use LionTech\SDK\Clients\RefundsClient;
use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\DTOs\Response\RefundResponse;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createRefundsClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $refundsClient = new RefundsClient($httpClient);

    return [$client, $requestFactory, $httpClient, $refundsClient];
}

function mockRefundResponse($requestFactory, $client, string $method, string $url, string $jsonBody): void
{
    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with($method, $url)
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
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

function refundResponseJson(): string
{
    return json_encode([
        'refundId' => 'ref_123',
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '50.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ]);
}

it('creates a refund', function (): void {
    [$client, $requestFactory, $httpClient, $refundsClient] = createRefundsClientMock();

    mockRefundResponse(
        $requestFactory,
        $client,
        'POST',
        'https://api.example.com/api/v1/merchant/refunds',
        refundResponseJson()
    );

    $request = new CreateRefundRequest(amount: new Money('50.00', Currency::USD), paymentId: 'pay_123');
    $result = $refundsClient->create($request);

    expect($result)
        ->toBeInstanceOf(RefundResponse::class);
    expect($result->refundId)
        ->toBe('ref_123');
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('creates a refund with merchant id', function (): void {
    [$client, $requestFactory, $httpClient, $refundsClient] = createRefundsClientMock();

    mockRefundResponse(
        $requestFactory,
        $client,
        'PUT',
        'https://api.example.com/api/v1/merchant/refunds/ref_merchant_1',
        refundResponseJson()
    );

    $request = new CreateRefundRequest(amount: new Money('50.00', Currency::USD), paymentId: 'pay_123');
    $result = $refundsClient->createWithId('ref_merchant_1', $request);

    expect($result)
        ->toBeInstanceOf(RefundResponse::class);
    expect($result->refundId)
        ->toBe('ref_123');
});

it('gets a refund', function (): void {
    [$client, $requestFactory, $httpClient, $refundsClient] = createRefundsClientMock();

    mockRefundResponse(
        $requestFactory,
        $client,
        'GET',
        'https://api.example.com/api/v1/merchant/refunds/ref_123',
        refundResponseJson()
    );

    $result = $refundsClient->get('ref_123');

    expect($result)
        ->toBeInstanceOf(RefundResponse::class);
    expect($result->refundId)
        ->toBe('ref_123');
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('checks refund is final', function (): void {
    [$client, $requestFactory, $httpClient, $refundsClient] = createRefundsClientMock();

    mockRefundResponse(
        $requestFactory,
        $client,
        'GET',
        'https://api.example.com/api/v1/merchant/refunds/ref_123',
        json_encode([
            'refundId' => 'ref_123',
            'paymentId' => 'pay_123',
            'amount' => [
                'value' => '50.00',
                'currency' => 'USD',
            ],
            'status' => 'SUCCEEDED',
            'createdAt' => '2024-01-01T00:00:00Z',
        ])
    );

    $result = $refundsClient->get('ref_123');

    expect($result->isFinal())
        ->toBeTrue();
    expect($result->isSuccessful())
        ->toBeTrue();
});
