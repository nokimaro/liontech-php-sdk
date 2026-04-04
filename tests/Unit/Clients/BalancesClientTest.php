<?php

declare(strict_types=1);

use LionTech\SDK\Clients\BalancesClient;
use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\Http\ApiClient;

function createBalancesClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $balancesClient = new BalancesClient($apiClient);

    return [$apiClient, $balancesClient];
}

it('lists balances', function (): void {
    [$apiClient, $balancesClient] = createBalancesClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/balances')
        ->andReturn(mockResponse([
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

it('handles empty balances', function (): void {
    [$apiClient, $balancesClient] = createBalancesClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/balances')
        ->andReturn(mockResponse([
            'items' => [],
        ]));

    $result = $balancesClient->list();

    expect($result)
        ->toBe([]);
});
