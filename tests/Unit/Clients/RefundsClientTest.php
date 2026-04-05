<?php

declare(strict_types=1);

use Nokimaro\LionTech\Clients\RefundsClient;
use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Requests\CreateRefundRequest;
use Nokimaro\LionTech\Responses\RefundResponse;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

function createRefundsClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $refundsClient = new RefundsClient($apiClient);

    return [$apiClient, $refundsClient];
}

function refundData(array $overrides = []): array
{
    return [
        'type' => 'REFUND',
        'object' => array_merge([
            'refundId' => 'ref_123',
            'paymentId' => 'pay_123',
            'amount' => [
                'value' => '50.00',
                'currency' => 'USD',
            ],
            'status' => 'SUCCEEDED',
            'createdAt' => '2024-01-01T00:00:00Z',
        ], $overrides),
        'error' => [
            'code' => 0,
            'description' => 'No error.',
        ],
    ];
}

it('creates a refund', function (): void {
    [$apiClient, $refundsClient] = createRefundsClient();
    $request = new CreateRefundRequest(amount: new Money(
        '50.00',
        Currency::USD
    ), paymentId: 'pay_123', webhookUrl: 'https://example.com/webhook');

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/refunds', $request)
        ->andReturn(mockResponse(refundData()));

    $result = $refundsClient->create($request);

    expect($result)
        ->toBeInstanceOf(RefundResponse::class);
    expect($result->refundId)
        ->toBe('ref_123');
});

it('creates a refund with merchant id', function (): void {
    [$apiClient, $refundsClient] = createRefundsClient();
    $request = new CreateRefundRequest(amount: new Money(
        '50.00',
        Currency::USD
    ), paymentId: 'pay_123', webhookUrl: 'https://example.com/webhook');

    $apiClient->shouldReceive('put')
        ->with('/api/v1/merchant/refunds/ref_merchant_1', $request)
        ->andReturn(mockResponse(refundData()));

    $result = $refundsClient->createWithId('ref_merchant_1', $request);

    expect($result)
        ->toBeInstanceOf(RefundResponse::class);
    expect($result->refundId)
        ->toBe('ref_123');
});

it('gets a refund', function (): void {
    [$apiClient, $refundsClient] = createRefundsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/refunds/ref_123')
        ->andReturn(mockResponse(refundData()));

    $result = $refundsClient->get('ref_123');

    expect($result)
        ->toBeInstanceOf(RefundResponse::class);
    expect($result->refundId)
        ->toBe('ref_123');
    expect($result->status->value)
        ->toBe('SUCCEEDED');
});

it('checks if refund is final', function (): void {
    [$apiClient, $refundsClient] = createRefundsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/refunds/ref_123')
        ->andReturn(mockResponse(refundData([
            'status' => 'SUCCEEDED',
        ])));

    $result = $refundsClient->get('ref_123');

    expect($result->isFinal())
        ->toBeTrue();
    expect($result->isSuccessful())
        ->toBeTrue();
});
