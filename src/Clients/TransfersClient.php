<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Json;

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

        $envelope = Json::decode((string) $response->getBody());
        return Json::assertArray($envelope['object']);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $transferId): array
    {
        $response = $this->apiClient->get('/api/v1/merchant/transfers/' . $transferId);

        $envelope = Json::decode((string) $response->getBody());
        return Json::assertArray($envelope['object']);
    }
}
