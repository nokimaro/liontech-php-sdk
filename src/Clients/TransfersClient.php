<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\Http\ApiClient;
use LionTech\SDK\Json;

final readonly class TransfersClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->apiClient->post('/api/v1/merchant/transfers', $data);

        return Json::decode((string) $response->getBody());
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $transferId): array
    {
        $response = $this->apiClient->get('/api/v1/merchant/transfers/' . $transferId);

        return Json::decode((string) $response->getBody());
    }
}
