<?php

declare(strict_types=1);

use Nokimaro\LionTech\Clients\PaymentsClient;
use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Requests\CreatePaymentRequest;
use Nokimaro\LionTech\Responses\PaymentResponse;
use Nokimaro\LionTech\Responses\RefundResponse;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\EncryptedCardData;
use Nokimaro\LionTech\ValueObjects\Money;
use Nokimaro\LionTech\ValueObjects\PaymentData;

function createPaymentsClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $paymentsClient = new PaymentsClient($apiClient);

    return [$apiClient, $paymentsClient];
}

function paymentData(array $overrides = []): array
{
    return [
        'type' => 'PAYMENT',
        'object' => array_merge([
            'paymentId' => 'pay_123',
            'amount' => [
                'value' => '100.00',
                'currency' => 'USD',
            ],
            'status' => 'RECONCILED',
            'createdAt' => '2024-01-01T00:00:00Z',
        ], $overrides),
        'error' => [
            'code' => 0,
            'description' => 'No error.',
        ],
    ];
}

it('creates a payment', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'enc'));
    $request = new CreatePaymentRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/payments', $request)
        ->andReturn(mockResponse(paymentData()));

    $result = $paymentsClient->create($request);

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->paymentId)
        ->toBe('pay_123');
    expect($result->status->value)
        ->toBe('RECONCILED');
});

it('creates a payment with merchant id', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'enc'));
    $request = new CreatePaymentRequest(amount: new Money('100.00', Currency::USD), paymentData: $paymentData);

    $apiClient->shouldReceive('put')
        ->with('/api/v1/merchant/payments/pay_merchant_1', $request)
        ->andReturn(mockResponse(paymentData()));

    $result = $paymentsClient->createWithId('pay_merchant_1', $request);

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('gets a payment', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/payments/pay_123')
        ->andReturn(mockResponse(paymentData()));

    $result = $paymentsClient->get('pay_123');

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->paymentId)
        ->toBe('pay_123');
});

it('confirms a payment', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/payments/pay_123/confirm')
        ->andReturn(mockResponse(paymentData([
            'status' => 'RECONCILED',
        ])));

    $result = $paymentsClient->confirm('pay_123');

    expect($result)
        ->toBeInstanceOf(PaymentResponse::class);
    expect($result->status->value)
        ->toBe('RECONCILED');
});

it('gets refunds for a payment', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/payments/pay_123/refunds')
        ->andReturn(mockResponse([
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

    $refunds = $paymentsClient->getRefunds('pay_123');

    expect($refunds)
        ->toHaveCount(1);
    expect($refunds[0])->toBeInstanceOf(RefundResponse::class);
    expect($refunds[0]->refundId)->toBe('ref_1');
});

it('checks if payment is final', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/payments/pay_123')
        ->andReturn(mockResponse(paymentData([
            'status' => 'RECONCILED',
        ])));

    $result = $paymentsClient->get('pay_123');

    expect($result->isFinal())
        ->toBeTrue();
    expect($result->isSuccessful())
        ->toBeTrue();
});

it('checks redirect requirement', function (): void {
    [$apiClient, $paymentsClient] = createPaymentsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/payments/pay_123')
        ->andReturn(mockResponse(paymentData([
            'status' => 'PENDING',
            'additionalAction' => [
                'action' => 'redirect',
                'value' => 'https://3ds.example.com',
            ],
        ])));

    $result = $paymentsClient->get('pay_123');

    expect($result->requiresRedirect())
        ->toBeTrue();
    expect($result->getRedirectUrl())
        ->toBe('https://3ds.example.com');
});
