<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class BalancesClient
{
    private const string BALANCES_PATH = '/api/v1/merchant/balances';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * List balances by currency.
     *
     * @return array<int, MerchantAccount>
     */
    public function list(): array
    {
        $response = $this->httpClient->get(self::BALANCES_PATH);
        $data = Json::decode((string) $response->getBody());

        $items = Json::assertArrayOfArrays($data['items'] ?? $data['accounts'] ?? []);

        $accounts = [];
        foreach ($items as $item) {
            $accounts[] = MerchantAccount::fromArray($item);
        }

        return $accounts;
    }
}
