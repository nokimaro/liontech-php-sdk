<?php

declare(strict_types=1);

use Nokimaro\LionTech\Clients\PayoutsClient;
use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Requests\CreatePayoutRequest;
use Nokimaro\LionTech\Responses\PayoutResponse;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\EncryptedCardData;
use Nokimaro\LionTech\ValueObjects\Money;
use Nokimaro\LionTech\ValueObjects\PaymentData;

function createPayoutsClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $payoutsClient = new PayoutsClient($apiClient);

    return [$apiClient, $payoutsClient];
}

function payoutData(array $overrides = []): array
{
    return [
        'type' => 'PAYOUT',
        'object' => array_merge([
            'payoutId' => 'payout_123',
            'amount' => [
                'value' => '500.00',
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

it('creates a payout', function (): void {
    [$apiClient, $payoutsClient] = createPayoutsClient();
    $paymentData = PaymentData::card(new EncryptedCardData(encryptedCardData: 'enc'));
    $request = new CreatePayoutRequest(amount: new Money('500.00', Currency::USD), paymentData: $paymentData);

    $apiClient->shouldReceive('put')
        ->with('/api/v1/merchant/payouts/payout_123', $request)
        ->andReturn(mockResponse(payoutData()));

    $result = $payoutsClient->createWithId('payout_123', $request);

    expect($result)
        ->toBeInstanceOf(PayoutResponse::class);
    expect($result->payoutId)
        ->toBe('payout_123');
    expect($result->status->value)
        ->toBe('SUCCEEDED');
});

it('gets a payout', function (): void {
    [$apiClient, $payoutsClient] = createPayoutsClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/payouts/payout_123')
        ->andReturn(mockResponse(payoutData()));

    $result = $payoutsClient->get('payout_123');

    expect($result)
        ->toBeInstanceOf(PayoutResponse::class);
    expect($result->payoutId)
        ->toBe('payout_123');
    expect($result->status->value)
        ->toBe('SUCCEEDED');
    expect($result->isFinal())
        ->toBeTrue();
    expect($result->isSuccessful())
        ->toBeTrue();
});
