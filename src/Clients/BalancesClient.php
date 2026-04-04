<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\Http\ApiClient;
use LionTech\SDK\Json;

final readonly class BalancesClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    /**
     * @return list<MerchantAccount>
     */
    public function list(): array
    {
        $response = $this->apiClient->get('/api/v1/merchant/balances');
        $data = Json::decode((string) $response->getBody());
        // @pest-mutate-ignore -- Defensive coalesce for API compatibility
        return array_map(
            MerchantAccount::fromArray(...),
            Json::assertArrayOfArrays($data['items'] ?? $data['accounts'] ?? []),
        );
    }
}
