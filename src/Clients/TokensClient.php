<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Response\SavedPaymentMethod;
use LionTech\SDK\Http\ApiClient;
use LionTech\SDK\Json;

final readonly class TokensClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    /**
     * @return list<SavedPaymentMethod>
     */
    public function list(?string $accountId = null, ?string $email = null, ?string $phone = null): array
    {
        $query = array_filter([
            'accountId' => $accountId,
            'email' => $email,
            'phone' => $phone,
        ], static fn (?string $v): bool => $v !== null);

        $response = $this->apiClient->get('/api/v1/merchant/tokens', $query);
        $data = Json::decode((string) $response->getBody());
        // @pest-mutate-ignore -- Defensive coalesce for API compatibility
        return array_map(
            SavedPaymentMethod::fromArray(...),
            Json::assertArrayOfArrays($data['saved_payment_methods'] ?? $data['items'] ?? []),
        );
    }

    public function delete(string $tokenId): void
    {
        $this->apiClient->delete('/api/v1/merchant/tokens/' . $tokenId);
    }
}
