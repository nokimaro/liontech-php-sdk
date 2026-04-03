<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\Http\HttpClient;

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
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $accounts = [];
        foreach ($data['items'] ?? $data['accounts'] ?? [] as $item) {
            $accounts[] = MerchantAccount::fromArray($item);
        }

        return $accounts;
    }
}
