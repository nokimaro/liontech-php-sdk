<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class TransfersClient
{
    private const string TRANSFERS_PATH = '/api/v1/merchant/transfers';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * Create a new transfer.
     *
     * @param array<string, mixed> $data Transfer data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post(self::TRANSFERS_PATH, $data);

        return Json::decode((string) $response->getBody());
    }

    /**
     * Get transfer information.
     *
     * @return array<string, mixed>
     */
    public function get(string $transferId): array
    {
        $response = $this->httpClient->get(self::TRANSFERS_PATH . '/' . $transferId);

        return Json::decode((string) $response->getBody());
    }
}
