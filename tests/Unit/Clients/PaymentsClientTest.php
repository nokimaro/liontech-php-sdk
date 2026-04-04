<?php

declare(strict_types=1);

use LionTech\SDK\Clients\PaymentsClient;
use LionTech\SDK\DTOs\Request\CreatePaymentRequest;
use LionTech\SDK\DTOs\Response\PaymentResponse;
use LionTech\SDK\DTOs\Response\RefundResponse;
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

function createPaymentsClientMock(): array
{
    $client = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $httpClient = new HttpClient('https://api.example.com', $client, $requestFactory);
    $paymentsClient = new PaymentsClient($httpClient);

    return [$client, $requestFactory, $httpClient, $paymentsClient];
}

function mockPaymentResponse($requestFactory, $client, string $method, string $url, string $jsonBody): void
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

function paymentResponseJson(): string
{
    return json_encode([
        'paymentId' => 'pay_123',
        'amount' => [
            'value' => '100.00',
            'currency' => 'USD',
        ],
        'status' => 'PENDING',
        'createdAt' => '2024-01-01T00:00:00Z',
    ]);
}

it('creates a payment', function (): void {
    [$client, $requestFactory, $httpClient, $paymentsClient] = createPaymentsClientMock();

    mockPaymentResponse(
        $requestFactory,
        $client,
        'POST',
        'https://api.example.com/api/v1/merchant/payments',
        paymentResponseJson()
    );

    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted_data'));
    $request = new CreatePaymentRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);
    $result = $paymentsClient->create($request);

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('creates a payment with merchant id', function (): void {
    [$client, $requestFactory, $httpClient, $paymentsClient] = createPaymentsClientMock();

    mockPaymentResponse(
        $requestFactory,
        $client,
        'PUT',
        'https://api.example.com/api/v1/merchant/payments/pay_merchant_1',
        paymentResponseJson()
    );

    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'encrypted_data'));
    $request = new CreatePaymentRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);
    $result = $paymentsClient->createWithId('pay_merchant_1', $request);

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('gets a payment', function (): void {
    [$client, $requestFactory, $httpClient, $paymentsClient] = createPaymentsClientMock();

    mockPaymentResponse(
        $requestFactory,
        $client,
        'GET',
        'https://api.example.com/api/v1/merchant/payments/pay_123',
        paymentResponseJson()
    );

    $result = $paymentsClient->get('pay_123');

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('confirms a payment', function (): void {
    [$client, $requestFactory, $httpClient, $paymentsClient] = createPaymentsClientMock();

    mockPaymentResponse(
        $requestFactory,
        $client,
        'POST',
        'https://api.example.com/api/v1/merchant/payments/pay_123/confirm',
        json_encode([
            'paymentId' => 'pay_123',
            'amount' => [
                'value' => '100.00',
                'currency' => 'USD',
            ],
            'status' => 'RECONCILED',
            'createdAt' => '2024-01-01T00:00:00Z',
        ])
    );

    $result = $paymentsClient->confirm('pay_123');

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->status->value)
        ->toBe('RECONCILED');
});

it('lists refunds for a payment', function (): void {
    [$client, $requestFactory, $httpClient, $paymentsClient] = createPaymentsClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/api/v1/merchant/payments/pay_123/refunds')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            'items' => [
                [
                    'refundId' => 'ref_1',
                    'paymentId' => 'pay_123',
                    'amount' => [
                        'value' => '50.00',
                        'currency' => 'USD',
                    ],
                    'status' => 'SUCCEEDED',
                    'createdAt' => '2024-01-01T00:00:00Z',
                ],
            ],
        ]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $paymentsClient->getRefunds('pay_123');

    expect($result)
        ->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(RefundResponse::class);
    expect($result[0]->refundId)->toBe('ref_1');
});

it('lists refunds with direct array response', function (): void {
    [$client, $requestFactory, $httpClient, $paymentsClient] = createPaymentsClientMock();

    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/api/v1/merchant/payments/pay_456/refunds')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $client->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode([
            [
                'refundId' => 'ref_2',
                'paymentId' => 'pay_456',
                'amount' => [
                    'value' => '25.00',
                    'currency' => 'USD',
                ],
                'status' => 'PENDING',
                'createdAt' => '2024-01-01T00:00:00Z',
            ],
        ]));
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $result = $paymentsClient->getRefunds('pay_456');

    expect($result)
        ->toHaveCount(1);
    expect($result[0]->refundId)->toBe('ref_2');
});
