<?php

declare(strict_types=1);

use Nokimaro\LionTech\Clients\TransfersClient;
use Nokimaro\LionTech\Http\ApiClient;

function createTransfersClient(): array
{
    $apiClient = Mockery::mock(ApiClient::class);
    $transfersClient = new TransfersClient($apiClient);

    return [$apiClient, $transfersClient];
}

it('creates a transfer', function (): void {
    [$apiClient, $transfersClient] = createTransfersClient();

    $apiClient->shouldReceive('post')
        ->with('/api/v1/merchant/transfers', [
            'amount' => '100.00',
        ])
        ->andReturn(mockResponse([
            'transferId' => 'tr_123',
        ]));

    $result = $transfersClient->create([
        'amount' => '100.00',
    ]);

    expect($result)
        ->toBe([
            'transferId' => 'tr_123',
        ]);
});

it('gets a transfer', function (): void {
    [$apiClient, $transfersClient] = createTransfersClient();

    $apiClient->shouldReceive('get')
        ->with('/api/v1/merchant/transfers/tr_123')
        ->andReturn(mockResponse([
            'transferId' => 'tr_123',
        ]));

    $result = $transfersClient->get('tr_123');

    expect($result)
        ->toBe([
            'transferId' => 'tr_123',
        ]);
});
